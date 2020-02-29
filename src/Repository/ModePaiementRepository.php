<?php

namespace App\Repository;

use App\Entity\ModePaiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ModePaiement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModePaiement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModePaiement[]    findAll()
 * @method ModePaiement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModePaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModePaiement::class);
    }
    
   
    public function findByLibelle($libelle)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('UPPER(m.libelle) LIKE :lib')
            ->setParameter('lib', strtoupper($libelle))
            ->getQuery()
            ->getOneOrNullResult();
    }
    

    /*
    public function findOneBySomeField($value): ?ModePaiement
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
