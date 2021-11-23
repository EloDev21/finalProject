<?php

namespace App\Repository;

use App\Entity\Circuits;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Circuits|null find($id, $lockMode = null, $lockVersion = null)
 * @method Circuits|null findOneBy(array $criteria, array $orderBy = null)
 * @method Circuits[]    findAll()
 * @method Circuits[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CircuitsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Circuits::class);
    }

    // /**
    //  * @return Circuits[] Returns an array of Circuits objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Circuits
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
