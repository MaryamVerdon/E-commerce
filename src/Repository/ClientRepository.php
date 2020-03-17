<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
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
        
        $qb = $this->createQueryBuilder('cl');


        if(isset($parameters['critere_tri'])){
            $triOrdre = 'ASC';
            if(isset($parameters['tri_ordre'])){
                $triOrdre = strtoupper($parameters['tri_ordre']);
            }
                $qb->orderBy('cl.' . $parameters['critere_tri'], $triOrdre);
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

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findTenLastclients($nbClient = 10)
    {
        return $this->createQueryBuilder('c')
            ->select('c.id','c.nom','c.prenom','c.email')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults($nbClient)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Client[] Returns an array of Client objects
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
    public function findOneBySomeField($value): ?Client
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
