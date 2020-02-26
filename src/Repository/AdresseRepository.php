<?php

namespace App\Repository;

use App\Entity\Adresse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Adresse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adresse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adresse[]    findAll()
 * @method Adresse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdresseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adresse::class);
    }

    // /**
    //  * @return Adresse[] Returns an array of Adresse objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Adresse
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
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

    function getQueryByClient($client){
        $qb = $this->createQueryBuilder('ccl');
        return $this->createQueryBuilder('c')
            ->select('c')
            ->leftJoin('c.client', 'cl')
            ->addSelect('cl')
            ->add('where', $qb->expr()->in('cl', ':cl') )
            ->setParameter('cl', $client);
    }
}
