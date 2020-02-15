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

    public function __construct(RegistryInterface $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Leader::class);

        $this->entityManager = $entityManager;
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

}