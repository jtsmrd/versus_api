<?php

namespace App\Repository;

use App\Entity\NotificationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NotificationType|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationType|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationType[]    findAll()
 * @method NotificationType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NotificationType::class);
    }

    public function getNotificationType(string $name): ?NotificationType
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
