<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Panier;
use App\Entity\client;
use App\Entity\Taille;
use App\Entity\LigneDeCommande;
use App\Entity\Commande;
use App\Entity\ModePaiement;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="commande")
     */
    public function getPanierFromClient(){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $panier = $this->getDoctrine()
        ->getRepository(Panier::class)
        ->findByClientId($client->getId());
        $modePaiement = $this->getDoctrine()->getRepository(ModePaiement::class)
            ->find(1);

        $p = $panier->getPanier();
        $newPanier = [];
        foreach($p as $id => $taille){
            foreach($taille as $idTaille => $quantite){
                $newPanier[] = [
                    'article' => $this->getDoctrine()->getRepository(Article::class)->find($id),
                    'taille' => $this->getDoctrine()->getRepository(Taille::class)->find($idTaille),
                    'qte' => $quantite,
                ];
            }
        }

        return $this->render('commande/index.html.twig' , [
            'controller_name' => 'CommandeController',
            'paniers' => $newPanier
        ]);
    }

   
    /**
     * @Route("/commande/new", name="commande_new")
     */
    public function newCommande(){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $panier = $this->getDoctrine()
        ->getRepository(Panier::class)
        ->findByClientId($client->getId());
        $modePaiement = $this->getDoctrine()->getRepository(ModePaiement::class)
            ->find(1);

        $p = $panier->getPanier();
        $newPanier = [];
        foreach($p as $id => $taille){
            foreach($taille as $idTaille => $quantite){
                $newPanier[] = [
                    'article' => $this->getDoctrine()->getRepository(Article::class)->find($id),
                    'taille' => $this->getDoctrine()->getRepository(Taille::class)->find($idTaille),
                    'qte' => $quantite,
                ];
            }
        }

        $LignesDeCommande = [];

        foreach($newPanier as $lignepanier){
            // tu cree la lignecommande
            $ligne = new LigneDeCommande();
            $ligne->setQte($lignepanier['qte']);
            $ligne->setArticle($lignepanier['article']);
            $em->persist($ligne);
            // tu l'insere dans le tableau avec $LignesDeCommande[] = (ta ligne)
            $LignesDeCommande[] = $ligne;
        }

        // tu cree la commande
        $commande = new Commande();
        $commande->setDate(new \dateTime('now'));
        $commande->setClient($client);
        $commande->setModePaiement($modePaiement);
        // tu insere le tableau $LignesDeCommande dans ta commande
        foreach($LignesDeCommande as $ligne){
            $commande->addLignesDeCommande($ligne);
        }
        
        $em->persist($commande);
        $em->flush();
        
        return $this->render('commande/commandeValide.html.twig' , [
            'controller_name' => 'CommandeController'
        ]);
    }
        
}
