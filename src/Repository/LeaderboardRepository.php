<?php

namespace App\Repository;

use App\Entity\Leaderboard;
use App\Entity\LeaderboardType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Leaderboard|null find($id, $lockMode = null, $lockVersion = null)
 * @method Leaderboard|null findOneBy(array $criteria, array $orderBy = null)
 * @method Leaderboard[]    findAll()
 * @method Leaderboard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeaderboardRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Leaderboard::class);
    }

    public function getLeaderboard(LeaderboardType $leaderboardType): ?Leaderboard
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.type = :type')
            ->setParameter('type', $leaderboardType)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
