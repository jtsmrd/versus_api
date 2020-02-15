<?php

namespace App\Repository;

use App\Entity\Competition;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PDO;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Competition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Competition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Competition[]    findAll()
 * @method Competition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompetitionRepository extends ServiceEntityRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(RegistryInterface $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Competition::class);

        $this->entityManager = $entityManager;
    }

    /**
     * @param $followedUserIds
     * @return Competition[] Returns an array of Competition objects
     */
//    public function findByFollowedUserIds($followedUserIds)
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.users IN (:ids)')
//            ->setParameter('ids', $followedUserIds)
//            ->andWhere('c.active = 1')
//            ->orderBy('c.startDate', 'DESC')
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function getWinningCompetitions(
        User $user,
        \DateTime $startDate,
        ?\DateTime $endDate
    ) {
        if ($endDate) {

            return $this->createQueryBuilder('c')
                ->select('c')
                ->andWhere('c.winnerUserId = :userId')
                ->andWhere('c.expireDate >= :startDate')
                ->andWhere('c.expireDate <= :endDate')
                ->setParameter('userId', $user->getId())
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate)
                ->getQuery()
                ->getResult()
            ;
        }
        else {
            return $this->createQueryBuilder('c')
                ->select('c')
                ->andWhere('c.winnerUserId = :userId')
                ->andWhere('c.expireDate >= :startDate')
                ->setParameter('userId', $user->getId())
                ->setParameter('startDate', $startDate)
                ->getQuery()
                ->getResult()
            ;
        }
    }
}
