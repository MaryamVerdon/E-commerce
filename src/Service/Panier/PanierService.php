<?php

namespace App\Service\Panier;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ArticleRepository;

class PanierService {

    protected $session;
    protected $articleRepository;

    public function __construct(SessionInterface $session, ArticleRepository $articleRepository)
    {
        $this->session = $session;
        $this->articleRepository = $articleRepository;
    }

    public function add(int $id, int $quantite = 1){
        $panier = $this->session->get('panier', []);
        
        if(!empty($panier[$id])){
            $panier[$id] += $quantite;
        }else{
            $panier[$id] = $quantite;
        }

        $this->session->set('panier', $panier);

        return $this->getNbArticles();
    }

    public function modify(int $id, int $quantite){
        $panier = $this->session->get('panier', []);
        
        $panier[$id] = $quantite;

        $this->session->set('panier', $panier);
    }

    public function remove(int $id){
        $panier = $this->session->get('panier', []);
        
        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);
    }

    public function getPanier(){
        $p = $this->session->get('panier', []);

        $panier = [];

        foreach($p as $id => $quantite){
            $panier[] = [
                'article' => $this->articleRepository->find($id),
                'quantite' => $quantite
            ];
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
}