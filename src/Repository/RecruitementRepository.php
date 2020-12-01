<?php

namespace App\Repository;

use App\Entity\Recruitement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recruitement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recruitement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recruitement[]    findAll()
 * @method Recruitement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecruitementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recruitement::class);
    }

    // /**
    //  * @return Recruitement[] Returns an array of Recruitement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Recruitement
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
