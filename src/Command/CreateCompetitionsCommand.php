<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-03-30
 * Time: 12:34
 */

namespace App\Command;

use App\Entity\Competition;
use App\Entity\Entry;
use App\Entity\Notification;
use App\Repository\EntryRepository;
use App\Repository\NotificationTypeRepository;
use DateInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCompetitionsCommand extends Command
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntryRepository
     */
    private $entryRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Collection
     */
    private $processedIds;

    public function __construct(
        LoggerInterface $logger,
        EntryRepository $entryRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->logger = $logger;
        $this->entryRepository = $entryRepository;
        $this->entityManager = $entityManager;
        $this->processedIds = new ArrayCollection();
    }

    protected function configure()
    {
        $this
            ->setName('app:createCompetitions')
            ->setDescription('Attempts to match entries in order to create competitions.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Add an initial value to the processed ids array - won't work empty.
        $this->processedIds->add(1);

        $this->logger->debug('Create competitions started');

        $unprocessedEntriesExist = true;

        while ($unprocessedEntriesExist) {

            $entry = $this->getEntry();

            if (!$entry) {
                $unprocessedEntriesExist = false;
                continue;
            }

            $match = $this->getEntryMatch($entry);

            if (!$match) {
                $this->processedIds->add($entry->getId());
                continue;
            }

            $this->createCompetition($entry, $match);
        }
    }

    private function getEntry(): ?Entry
    {
        $query = $this->entityManager->createQuery(
            'select e.id from App\Entity\Entry e 
            where e.matched = 0
            and e.id not in (:param)
            order by e.createDate asc'
        );

        $query->setParameter('param', $this->processedIds->toArray());
        $query->setMaxResults(1);
        $entryId = $query->getOneOrNullResult();


        if (!$entryId) {
            return null;
        }

        return $this->entryRepository->find($entryId);
    }

    private function getEntryMatch(Entry $entry): ?Entry
    {
        $query = $this->entityManager->createQuery(
            "select e.id from App\Entity\Entry e 
            where e.matched = 0
            and e.typeId = :typeId
            and e.categoryId = :categoryId
            and e.rankId = :rankId
            and e.id != :id
            and e.user != :user
            order by e.createDate asc"
        );

        $query->setParameter('typeId', $entry->getTypeId());
        $query->setParameter('categoryId', $entry->getCategoryId());
        $query->setParameter('rankId', $entry->getRankId());
        $query->setParameter('id', $entry->getId());
        $query->setParameter('user', $entry->getUser());
        $query->setMaxResults(1);
        $matchId = $query->getOneOrNullResult();

        if (!$matchId) {
            return null;
        }

        return $this->entryRepository->find($matchId);
    }

    private function createCompetition(Entry $leftEntry, Entry $rightEntry)
    {
        $startDate = new \DateTime();

        // Now + 1 day
        $expireDate = $startDate->add(new DateInterval('P1D'));

        $featured = false;

        if ($leftEntry->getFeatured() || $rightEntry->getFeatured()) {
            $featured = true;
        }

        // TODO: Validate left and right Entry types match
        $categoryId = $leftEntry->getCategoryId();
        $typeId = $leftEntry->getTypeId();

        // Get User records
        $leftEntryUser = $leftEntry->getUser();
        $rightEntryUser = $rightEntry->getUser();

        $competition = new Competition();
        $competition->setStartDate($startDate);
        $competition->setExpireDate($expireDate);
        $competition->setFeatured($featured);
        $competition->setCategoryId($categoryId);
        $competition->setTypeId($typeId);
        $competition->setLeftEntry($leftEntry);
        $competition->setRightEntry($rightEntry);
        $competition->addUser($leftEntryUser);
        $competition->addUser($rightEntryUser);

        // Set matchDate and matched flag
        $leftEntry->setMatchDate($startDate);
        $leftEntry->setMatched(true);
        $this->entityManager->persist($leftEntry);

        // Set matchDate and matched flag
        $rightEntry->setMatchDate($startDate);
        $rightEntry->setMatched(true);
        $this->entityManager->persist($rightEntry);

        $this->entityManager->persist($competition);
        $this->entityManager->flush();

        $this->logger->debug('Competition created');
    }
}