<?php

namespace App\Repository;

use App\Entity\StatutCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method StatutCommande|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatutCommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatutCommande[]    findAll()
 * @method StatutCommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatutCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatutCommande::class);
    }

    // /**
    //  * @return StatutCommande[] Returns an array of StatutCommande objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StatutCommande
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
