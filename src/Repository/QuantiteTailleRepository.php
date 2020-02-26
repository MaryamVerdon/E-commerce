<?php

namespace App\Repository;

use App\Entity\QuantiteTaille;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method QuantiteTaille|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuantiteTaille|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuantiteTaille[]    findAll()
 * @method QuantiteTaille[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuantiteTailleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuantiteTaille::class);
    }

    // /**
    //  * @return QuantiteTaille[] Returns an array of QuantiteTaille objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuantiteTaille
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findQuantiteTailleByArticleAndTaille($article, $taille): ?QuantiteTaille
{
    return $this->createQueryBuilder('q')
        ->select('q')
        ->andWhere('q.article IN(:art)')
        ->setParameter('art', $article)
        ->andWhere('q.taille IN(:tai)')
        ->setParameter('tai', $taille)
        ->getQuery()
        ->getOneOrNullResult()
    ;
}
}
