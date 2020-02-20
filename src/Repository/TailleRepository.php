<?php

namespace App\Repository;

use App\Entity\Taille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Taille|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taille|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taille[]    findAll()
 * @method Taille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TailleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taille::class);
    }

    // /**
    //  * @return Taille[] Returns an array of Taille objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Taille
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
