<?php

namespace App\Service\Panier;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Repository\ArticleRepository;
use App\Repository\TailleRepository;

class PanierService {

    protected $session;
    protected $articleRepository;
    protected $tailleRepository;

    public function __construct(SessionInterface $session, ArticleRepository $articleRepository, TailleRepository $tailleRepository)
    {
        $this->session = $session;
        $this->articleRepository = $articleRepository;
        $this->tailleRepository = $tailleRepository;
    }

    
    public function add(int $id, int $idTaille, int $quantite = 1){
        $panier = $this->session->get('panier', []);
        
        if(!empty($panier[$id][$idTaille])){
            $panier[$id][$idTaille] += $quantite;
        }else{
            $panier[$id][$idTaille] = $quantite;
        }

        $this->session->set('panier', $panier);

        return $this->getNbArticles();
    }

    public function modify(int $id, int $idTaille, int $quantite){
        $panier = $this->session->get('panier', []);
        
        $panier[$id][$idTaille] = $quantite;

        $this->session->set('panier', $panier);
    }

    public function remove(int $id, int $idTaille){
        $panier = $this->session->get('panier', []);
        
        if(!empty($panier[$id][$idTaille])){
            unset($panier[$id][$idTaille]);
        }

        $this->session->set('panier', $panier);
    }

    public function getPanier(){
        $p = $this->session->get('panier', []);

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
}