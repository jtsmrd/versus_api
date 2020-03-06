<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2020-02-08
 * Time: 16:39
 */

namespace App\Command;


use App\Entity\Competition;
use App\Entity\Leader;
use App\Entity\LeaderboardType;
use App\Entity\User;
use App\Repository\CompetitionRepository;
use App\Repository\LeaderboardRepository;
use App\Repository\LeaderboardTypeRepository;
use App\Repository\LeaderRepository;
use App\Repository\UserRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessCompletedCompetitionsCommand extends Command
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LeaderRepository
     */
    private $leaderRepository;

    /**
     * @var LeaderboardTypeRepository
     */
    private $leaderboardTypeRepository;

    /**
     * @var LeaderboardRepository
     */
    private $leaderboardRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        LoggerInterface $logger,
        CompetitionRepository $competitionRepository,
        EntityManagerInterface $entityManager,
        LeaderRepository $leaderRepository,
        LeaderboardTypeRepository $leaderboardTypeRepository,
        LeaderboardRepository $leaderboardRepository,
        UserRepository $userRepository
    ) {
        parent::__construct();
        $this->logger = $logger;
        $this->competitionRepository = $competitionRepository;
        $this->entityManager = $entityManager;
        $this->leaderRepository = $leaderRepository;
        $this->leaderboardTypeRepository = $leaderboardTypeRepository;
        $this->userRepository = $userRepository;
        $this->leaderboardRepository = $leaderboardRepository;
    }

    protected function configure()
    {
        $this
            ->setName('app:processCompletedCompetitions')
            ->setDescription('Attempts to determine the winners of competitions that have ended, and update related records.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $unprocessedCompetitionsExist = true;

        while ($unprocessedCompetitionsExist) {

            $completedCompetitions = $this->getCompletedCompetitions();

            if (!$completedCompetitions) {
                $unprocessedCompetitionsExist = false;
                $this->logger->debug('No more unprocessed competitions');
                continue;
            }

            foreach ($completedCompetitions as $competition) {

                $this->processCompletedCompetition($competition);
            }
        }

        // Update leaderbards with leader image
        $this->updateLeaderboardFeatureImages();
    }

    private function getCompletedCompetitions()
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select(['c'])
            ->from(Competition::class, 'c')
            ->andWhere('c.winnerUserId is null')
            ->orderBy('c.expireDate', 'ASC')
            ->setMaxResults(10)
        ;
        return $queryBuilder->getQuery()->getResult();
    }

    private function processCompletedCompetition(Competition $competition) {

        $winningUser = null;
        $winningVoteCount = null;
        $leftEntry = $competition->getLeftEntry();
        $rightEntry = $competition->getRightEntry();

        if ($leftEntry->getVoteCount() > $rightEntry->getVoteCount()) {
            $winningUser = $leftEntry->getUser();
            $winningVoteCount = $leftEntry->getVoteCount();
        }
        elseif ($rightEntry->getVoteCount() > $leftEntry->getVoteCount()) {
            $winningUser = $rightEntry->getUser();
            $winningVoteCount = $rightEntry->getVoteCount();
        }

        $this->updateCompetitionRecord($competition, $winningUser, $winningVoteCount);

        $this->updateUserWins($winningUser);

        $this->updateLeader($winningUser, 'Weekly');
        $this->updateLeader($winningUser, 'Monthly');
        $this->updateLeader($winningUser, 'All Time');
    }

    private function updateCompetitionRecord(
        Competition $competition,
        User $winningUser,
        int $voteCount
    ) {
        $competition->setWinnerUserId($winningUser->getId());
        $competition->setActive(false);
        $competition->setWinnerVoteCount($voteCount);
        $this->entityManager->persist($competition);
        $this->entityManager->flush();
    }

    private function updateUserWins(User $winningUser) {
        $newWinCount = $winningUser->getTotalWins();
        $winningUser->setTotalWins($newWinCount);
        $this->entityManager->persist($winningUser);
        $this->entityManager->flush();
    }

    private function updateLeader(User $user, string $leaderboardType)
    {
        $startDate = null;
        $endDate = null;

        if ($leaderboardType == 'Weekly') {

            $lastSundayDate = date("Y-m-d H:i:s", strtotime('last sunday'));
            $startDate = date_create_from_format('Y-m-d H:i:s', $lastSundayDate);

            $endDate = clone($startDate);
            $dateInterval = new DateInterval('P1W');
            $endDate = $endDate->add($dateInterval);
        }
        else if ($leaderboardType == 'Monthly') {

            $firstDayOfThisMonth = date("Y-m-d", strtotime('first day of this month')) . ' 00:00:00';
            $startDate = date_create_from_format('Y-m-d H:i:s', $firstDayOfThisMonth);

            $endDate = clone($startDate);
            $dateInterval = new DateInterval('P1M');
            $endDate = $endDate->add($dateInterval);
        }
        else if ($leaderboardType == 'All Time') {

            $epochDate = date("Y-m-d H:i:s", 0);
            $startDate = date_create_from_format('Y-m-d H:i:s', $epochDate);
        }
        else {
            // Log error
            return;
        }

        $winningCompetitions = $this->competitionRepository->getWinningCompetitions(
            $user,
            $startDate,
            $endDate
        );

        if (count($winningCompetitions) == 0) {
            return;
        }

        $leaderboardType = $this->leaderboardTypeRepository->getLeaderboardType($leaderboardType);

        $leaderRecord = $this->leaderRepository->getLeaderRecord(
            $user,
            $leaderboardType,
            $startDate
        );

        if ($leaderRecord == null) {
            $leaderRecord = $this->createLeaderRecord(
                $user,
                $leaderboardType,
                $startDate
            );
        }

        $leaderRecord->setWinCount(count($winningCompetitions));

        $voteCount = 0;

        foreach ($winningCompetitions as $competition) {
            if ($competition instanceof Competition) {
                $voteCount += $competition->getWinnerVoteCount();
            }
        }

        $leaderRecord->setVoteCount($voteCount);

        $this->entityManager->persist($leaderRecord);
        $this->entityManager->flush();
    }

    private function createLeaderRecord(
        User $user,
        LeaderboardType $leaderboardType,
        \DateTime $startDate
    ): Leader
    {
        $leader = new Leader();
        $leader->setUser($user);
        $leader->setLeaderboardType($leaderboardType);
        $leader->setStartDate($startDate);
        return $leader;
    }

    private function updateLeaderboardFeatureImages()
    {
        $this->updateLeaderboardFeatureImage('Weekly');

        $this->updateLeaderboardFeatureImage('Monthly');

        $this->updateLeaderboardFeatureImage('All Time');
    }

    private function updateLeaderboardFeatureImage(string $leaderboardTypeName)
    {
        $leaderboardType = $this->leaderboardTypeRepository->getLeaderboardType($leaderboardTypeName);

        $topWeeklyLeader = $this->leaderRepository->getTopLeader($leaderboardType);

        if ($topWeeklyLeader) {

            $leaderboard = $this->leaderboardRepository->getLeaderboard($leaderboardType);

            $leaderboard->setFeatureImage($topWeeklyLeader->getUser()->getProfileImage());

            $this->entityManager->persist($leaderboard);
            $this->entityManager->flush();
        }
    }
}