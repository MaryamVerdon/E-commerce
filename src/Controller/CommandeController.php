<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Entity\Panier;
use App\Entity\PanierS;
use App\Entity\client;
use App\Entity\Taille;
use App\Entity\LigneDeCommande;
use App\Entity\StatutCommande;
use App\Entity\Commande;
use App\Entity\ModePaiement;
use App\Service\Panier\PanierService;
use App\Entity\Adresse;

class CommandeController extends AbstractController
{
    public function __construct(PanierService $PanierService){
        $this->PanierService = $PanierService;
    }
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
        $modePaiement = new ModePaiement();
        
        
        $modePaiement = $this->getDoctrine()->getRepository(ModePaiement::class)
            ->find(2);
            
        $p = $this->PanierService;
        $total = $p->getTotal();
        //dd($p->getPanier());

        /*$newPanier = [];
        foreach($p as $id => $taille){
            foreach($taille as $idTaille => $quantite){
                $newPanier[] = [
                    'article' => $this->getDoctrine()->getRepository(Article::class)->find($id),
                    'taille' => $this->getDoctrine()->getRepository(Taille::class)->find($idTaille),
                    'qte' => $quantite,
                    
                ];
            }
        }*/
        $adresse = $this->getDoctrine()->getRepository(Adresse::class)->findByClient($client->getId());
        //dd($adresse);
        return $this->render('commande/index.html.twig' , [
            'controller_name' => 'CommandeController',
            'panier' => $p->getPanier(),
            'adresses' => $adresse,
            'prixTot' => $total
        ]);
    }

   
    /**
     * @Route("/commande/new", name="commande_new")
     */
    public function newCommande(Request $request){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $adresse = $request->query->get('adresse');
        //dd($adresse);
        $ad = $this->getDoctrine()
        ->getRepository(Adresse::class)
        ->find($adresse);
        //dd($ad);
        $panier = $this->getDoctrine()
        ->getRepository(Panier::class)
        ->findByClientId($client->getId());
      
       
        $modePaiement = $this->getDoctrine()->getRepository(ModePaiement::class)
            ->find(2);
        
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
            $ligne->setTaille($lignepanier['taille']);
            $ligne->setArticle($lignepanier['article']);
            $em->persist($ligne);
            // tu l'insere dans le tableau avec $LignesDeCommande[] = (ta ligne)
            $LignesDeCommande[] = $ligne;
        }
        //recuperation des statue de commande
        $statut = $this->getDoctrine()
        ->getRepository(StatutCommande::class)
        ->findByCode(1);
        //dd($statut);
        // tu cree la commande
        $commande = new Commande();
        $commande->setDate(new \dateTime('now'));
        $commande->setClient($client);
        $commande->setAdresse($ad);
        $commande->setModePaiement($modePaiement);
        $commande->setFraisDePort(2.99);
        $commande->setStatutCommande($statut[0]);
        // tu insere le tableau $LignesDeCommande dans ta commande
        foreach($LignesDeCommande as $ligne){
            $commande->addLigneDeCommande($ligne);
        }
        
        $em->persist($commande);
        $em->flush();
        
        return $this->render('commande/commandeValide.html.twig' , [
            'controller_name' => 'CommandeController'
        ]);
    }
        
}
