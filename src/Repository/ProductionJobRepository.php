<?php

namespace App\Repository;

use App\Entity\ProductionJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductionJob|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductionJob|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductionJob[]    findAll()
 * @method ProductionJob[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductionJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductionJob::class);
    }

    // /**
    //  * @return ProductionJob[] Returns an array of ProductionJob objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductionJob
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
