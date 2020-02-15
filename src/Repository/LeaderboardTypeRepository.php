<?php

namespace App\Repository;

use App\Entity\LeaderboardType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method LeaderboardType|null find($id, $lockMode = null, $lockVersion = null)
 * @method LeaderboardType|null findOneBy(array $criteria, array $orderBy = null)
 * @method LeaderboardType[]    findAll()
 * @method LeaderboardType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeaderboardTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, LeaderboardType::class);
    }

    // /**
    //  * @return LeaderboardType[] Returns an array of LeaderboardType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    public function getLeaderboardType(string $name): ?LeaderboardType
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
