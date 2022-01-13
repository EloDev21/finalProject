<?php

namespace App\Repository;

use App\Entity\ContacForm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ContacForm|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContacForm|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContacForm[]    findAll()
 * @method ContacForm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContacFormRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContacForm::class);
    }

    // /**
    //  * @return ContacForm[] Returns an array of ContacForm objects
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
    public function findOneBySomeField($value): ?ContacForm
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
