<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    function findByParametersPagine($page, $nbMaxParPage = 20, $parameters = []){
        if(!is_numeric($page)){
            throw new InvalidArgumentException('La valeur de l\'argument $page est incorrecte (valeur : ' . $page . ').');
        }
        if($page < 1){
            throw new InvalidArgumentException('La page demandé n\'existe pas.');
        }
        if(!is_numeric($nbMaxParPage) || $nbMaxParPage < 1){
            throw new InvalidArgumentException('La valeur de l\'argument $nbMaxParPage est incorrecte (valeur : ' . $nbMaxParPage . ').');
        }
        
        $qb = $this->createQueryBuilder('c');


        if(isset($parameters['critere_tri'])){
            $triOrdre = 'ASC';
            if(isset($parameters['tri_ordre'])){
                $triOrdre = strtoupper($parameters['tri_ordre']);
            }
            if($parameters['critere_tri'] === 'nom'){
                $qb->join("c.client", "cl")
                    ->orderBy('cl.nom', $triOrdre);
            }else if($parameters['critere_tri'] === 'statut'){
                $qb->join("c.statut_commande", "s")
                    ->orderBy('s.libelle', $triOrdre);
            }else{
                $qb->orderBy('c.' . $parameters['critere_tri'], $triOrdre);
            }
        }

        $debut = ($page -1) * $nbMaxParPage;
        $query = $qb->getQuery();
        $query->setFirstResult($debut)
            ->setMaxResults($nbMaxParPage);

        $paginator = new Paginator($query);

        if($paginator->count() <= $debut && $page != 1){
            throw new NotFoundHttpException('La page demandée n\'existe pas.');
        }

        return $paginator;
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
