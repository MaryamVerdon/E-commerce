<?php

namespace App\Repository;

use App\Entity\TypeArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TypeArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeArticle[]    findAll()
 * @method TypeArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeArticle::class);
    }

    // /**
    //  * @return TypeArticle[] Returns an array of TypeArticle objects
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
    public function findOneBySomeField($value): ?TypeArticle
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
