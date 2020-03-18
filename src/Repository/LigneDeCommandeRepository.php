<?php

namespace App\Repository;

use App\Entity\LigneDeCommande;
use App\Entity\Section;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LigneDeCommande|null find($id, $lockMode = null, $lockVersion = null)
 * @method LigneDeCommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method LigneDeCommande[]    findAll()
 * @method LigneDeCommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LigneDeCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneDeCommande::class);
    }
    
    public function findMostSoldArticles($nbArticles = 1)
    {
        return $this->createQueryBuilder("l")
            ->select('a.id','a.libelle','a.description','a.prix_u','a.image','count(l.article)')
            ->join('l.article', 'a')
            ->groupBy('a.id','a.libelle','a.description','a.prix_u','a.image')
            ->orderBy('sum(l.article)','DESC')
            ->setMaxResults($nbArticles)
            ->getQuery()
            ->getResult();
    }

    /*
    public function findMostSoldArticlesSections()
    {
        return $this->createQueryBuilder("l")
            ->select('a.image','s.libelle')
            ->join('l.article', 'a')
            ->join('a.sections', 's')
            ->groupBy('s.libelle', 'a.image')
            ->orderBy('sum(l.qte)','DESC')
            ->getQuery()
            ->getResult();
    }
    */

    public function findMostSoldArticlesSections()
    {
        $sections = $this->getEntityManager()
            ->getRepository(Section::class)
            ->findAll();

        $articlesSections = [];

        foreach($sections as $section){
            $qb = $this->createQueryBuilder("ls");
            $articlesSections[] = $this->createQueryBuilder("l")
                ->select('a.image','s.libelle')
                ->join('l.article', 'a')
                ->join('a.sections', 's')
                ->add('where', $qb->expr()->in('s', ':s'))
                ->setParameter('s',$section)
                ->orderBy('sum(l.qte)','DESC')
                ->groupBy('s.libelle', 'a.image')
                ->getQuery()
                ->getResult()[0];
        }

        return $articlesSections;
    }

    // /**
    //  * @return LigneDeCommande[] Returns an array of LigneDeCommande objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LigneDeCommande
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
