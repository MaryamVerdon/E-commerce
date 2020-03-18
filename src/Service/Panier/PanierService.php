<?php

namespace App\Service\Panier;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ArticleRepository;
use App\Repository\TailleRepository;
use App\Repository\PanierRepository;
use App\Entity\Panier;
use App\Entity\Article;
use App\Entity\QuantiteTaille;

class PanierService {

    protected $session;
    protected $articleRepository;
    protected $tailleRepository;
    protected $panierRepository;
    protected $entityManager;
    private $security;

    public function __construct(SessionInterface $session, Security $security, ArticleRepository $articleRepository, TailleRepository $tailleRepository, PanierRepository $panierRepository, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->articleRepository = $articleRepository;
        $this->tailleRepository = $tailleRepository;
        $this->panierRepository = $panierRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    
    public function add(int $id, int $idTaille, int $quantite = 1){
        $panier = $this->session->get('panier', []);
        
        if(!empty($panier[$id][$idTaille])){
            $panier[$id][$idTaille] += $quantite;
        }else{
            $panier[$id][$idTaille] = $quantite;
        }

        $this->session->set('panier', $panier);

        $this->storePanierDb($panier);

        return $this->getNbArticles();
    }

    public function modify(int $id, int $idTaille, int $quantite){
        $panier = $this->session->get('panier', []);
        
        $panier[$id][$idTaille] = $quantite;

        $this->session->set('panier', $panier);

        $this->storePanierDb($panier);
    }

    public function remove(int $id, int $idTaille){
        $panier = $this->session->get('panier', []);
        
        if(!empty($panier[$id][$idTaille])){
            unset($panier[$id][$idTaille]);
        }
        if(empty($panier[$id])){
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);

        $this->storePanierDb($panier);
    }

    public function getPanier(){
        $p = $this->session->get('panier', []);

        $panierDb = $this->retrievePanierDb();
        if($panierDb){
            if($p == []){
                $p = $panierDb;
            }else if($p != $panierDb){
                $p = $this->additionArray($p, $panierDb);
                $this->session->set('panier', $p);
                $this->storePanierDb($p);
            }
        }

        $panier = [];

        foreach($p as $id => $taille){
            foreach($taille as $idTaille => $quantite){
                $panier[] = [
                    'article' => $this->articleRepository->find($id),
                    'taille' => $this->tailleRepository->find($idTaille),
                    'quantite' => $quantite
                ];
            }
        }

        return $panier;
    }

    public function overwritePanierDBFromSession(){
        $p = $this->session->get('panier', []);
        $this->storePanierDb($p);
    }

    public function getTotal(){
        $panier = $this->getPanier();
        $total = 0;

        foreach($panier as $ligne){
            $totalLigne = $ligne['article']->getPrixU() * $ligne['quantite'];
            $total += $totalLigne;
        }

        return $total;
    }

    public function getNbArticles(){
        $panier = $this->getPanier();
        $total = 0;

        foreach($panier as $ligne){
            $total += $ligne['quantite'];
        }

        return $total;
    }

    public function getPanierTest(){
        $p = $this->session->get('panier', []);

        return $p;
    }

    public function getQteFor(int $id, int $idTaille){
        $panier = $this->getPanier();
        $total = 0;

        if(isset($panier[$id][$idTaille])){
            $total = $panier[$id][$idTaille];
        }

        return $total;
    }

    public function getPanierTest2(){
        $res = [];
        $p = $this->session->get('panier', []);

        $p1 = $this->retrievePanierDb();
        
        $p2 = $this->additionArray($p, $p1);

        return ['panier-session' => $p, 'panier-bdd' => $p1, 'panier-merge' => $p2];
    }

    private function additionArray(...$arrays){
        $res = [];
        foreach($arrays as $array){
            foreach($array as $id => $tailles){
                foreach($tailles as $idTaille => $quantite){
                    $res[$id][$idTaille] = ((isset($res[$id][$idTaille]) ? $res[$id][$idTaille] : 0 ) + $quantite);
                }
            }
        }
        return $res;
    }

    public function clear(){
        $panier = [];

        $this->session->set('panier', $panier);

        $this->storePanierDb($panier);
    }

    private function storePanierDb(array $panier){
        $user = $this->security->getUser();
        if($user){
            $clientId = $user->getId();
            $panierC = $this->panierRepository->findByClientId($clientId);
            if($panier != []){
                if(!$panierC){
                    $panierC = new Panier();
                    $panierC->setClientId($clientId);
                }
                $panierC->setPanier($panier);
                $this->entityManager->merge($panierC);
                $this->entityManager->flush();
            }else if($panier == []){
                if($panierC){
                    $this->entityManager->remove($panierC);
                    $this->entityManager->flush();
                }
            }
        }
    }

    private function retrievePanierDb(){
        $user = $this->security->getUser();
        if($user){
            $clientId = $user->getId();
            $panierC = $this->panierRepository->findByClientId($clientId);
            if($panierC){
                return $panierC->getPanier();
            }else{
                return null;
            }
        }
    }
}
