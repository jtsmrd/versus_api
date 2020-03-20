<?php

namespace App\Repository;

use App\Entity\Leader;
use App\Entity\LeaderboardType;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Leader|null find($id, $lockMode = null, $lockVersion = null)
 * @method Leader|null findOneBy(array $criteria, array $orderBy = null)
 * @method Leader[]    findAll()
 * @method Leader[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeaderRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LeaderboardTypeRepository
     */
    private $leaderboardTypeRepository;

    public function __construct(
        RegistryInterface $registry,
        EntityManagerInterface $entityManager,
        LeaderboardTypeRepository $leaderboardTypeRepository
    ) {
        parent::__construct($registry, Leader::class);

        $this->entityManager = $entityManager;
        $this->leaderboardTypeRepository = $leaderboardTypeRepository;
    }

    public function getLeaderRecord(
        User $user,
        LeaderboardType $leaderboardType,
        \DateTime $startDate
    ): ?Leader
    {
        $query = $this->createQueryBuilder('l')
            ->andWhere('l.user = :user')
            ->andWhere('l.leaderboardType = :leaderboardType')
            ->andWhere('l.startDate = :startDate')
            ->setParameter('user', $user)
            ->setParameter('leaderboardType', $leaderboardType)
            ->setParameter('startDate', $startDate)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function getWeeklyLeaders()
    {
        $leaderboardType = $this->leaderboardTypeRepository->getLeaderboardType('Weekly');

        if ($leaderboardType == null) {
            return [];
        }

        $lastSundayDate = date("Y-m-d H:i:s", strtotime('last sunday'));
        $startDate = date_create_from_format('Y-m-d H:i:s', $lastSundayDate);

        return $this->getLeaders($leaderboardType, $startDate);
    }

    public function getMonthlyLeaders()
    {
        $leaderboardType = $this->leaderboardTypeRepository->getLeaderboardType('Monthly');

        if ($leaderboardType == null) {
            return [];
        }

        $firstDayOfThisMonth = date("Y-m-d", strtotime('first day of this month')) . ' 00:00:00';
        $startDate = date_create_from_format('Y-m-d H:i:s', $firstDayOfThisMonth);

        return $this->getLeaders($leaderboardType, $startDate);
    }

    public function getAllTimeLeaders()
    {
        $leaderboardType = $this->leaderboardTypeRepository->getLeaderboardType('All Time');

        if ($leaderboardType == null) {
            return [];
        }

        $epochDate = date("Y-m-d H:i:s", 0);
        $startDate = date_create_from_format('Y-m-d H:i:s', $epochDate);

        return $this->getLeaders($leaderboardType, $startDate);
    }

    public function getTopLeader(LeaderboardType $leaderboardType): ?Leader
    {
        $query = $this->createQueryBuilder('l')
            ->innerJoin('l.user', 'u')
            ->addSelect('u')
            ->andWhere('l.leaderboardType = :leaderboardType')
            ->setParameter('leaderboardType', $leaderboardType)
            ->orderBy('l.winCount', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    private function getLeaders(LeaderboardType $leaderboardType, \DateTime $startDate)
    {
        $query = $this->createQueryBuilder('l')
            ->innerJoin('l.user', 'u')
            ->addSelect('u')
            ->andWhere('l.leaderboardType = :leaderboardType')
            ->andWhere('l.startDate = :startDate')
            ->setParameter('leaderboardType', $leaderboardType)
            ->setParameter('startDate', $startDate)
            ->orderBy('l.winCount', 'DESC')
            ->addOrderBy('l.voteCount', 'DESC')
            ->setMaxResults(50)
            ->getQuery();

        return $query->getResult();
    }
}