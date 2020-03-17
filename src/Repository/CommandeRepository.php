<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    function findByClient($client){
        $qb = $this->createQueryBuilder('ccl');
        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.client', 'cl')
            ->addSelect('cl')
            ->add('where', $qb->expr()->in('cl', ':cl') )
            ->setParameter('cl', $client)
            ->getQuery()
            ->getResult();
    }

    function findLastByClient($client){
        $qb = $this->createQueryBuilder('ccl');
        $res = $this->createQueryBuilder('c')
            ->select('c')
            ->addOrderBy('c.date', 'DESC')
            ->leftJoin('c.client', 'cl')
            ->addSelect('cl')
            ->add('where', $qb->expr()->in('cl', ':cl') )
            ->setParameter('cl', $client)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
        return $res[0];
    }    
    
    function findLastsByClient($client, $nbResults = 10){
        $qb = $this->createQueryBuilder('ccl');
        return $this->createQueryBuilder('c')
            ->select('c')
            ->addOrderBy('c.date', 'DESC')
            ->leftJoin('c.client', 'cl')
            ->addSelect('cl')
            ->add('where', $qb->expr()->in('cl', ':cl') )
            ->setParameter('cl', $client)
            ->setMaxResults($nbResults)
            ->getQuery()
            ->getResult();
    }
    function findTenLastCommandes($nbResults = 10){
        return $this->createQueryBuilder('c')
            ->select('c')
            ->addOrderBy('c.date', 'DESC')
            ->setMaxResults($nbResults)
            ->getQuery()
            ->getResult();
    }
    // /**
    //  * @return Commande[] Returns an array of Commande objects
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
    public function findOneBySomeField($value): ?Commande
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
