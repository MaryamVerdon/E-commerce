<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\QuantiteTaille;
use App\Entity\Article;
use App\Entity\Panier;
use App\Entity\PanierS;
use App\Entity\client;
use App\Entity\Taille;
use App\Entity\LigneDeCommande;
use App\Entity\StatutCommande;
use App\Entity\Commande;
use App\Entity\ModePaiement;
use App\Service\Payment;
use App\Service\Panier\PanierService;
use App\Service\Mailer\MailerService;
use App\Entity\Adresse;
use App\Form\AdresseType;

class CommandeController extends AbstractController
{
    public function __construct(PanierService $PanierService){
        $this->PanierService = $PanierService;
    }
    /**
     * @Route("/commande", name="commande")
     */
    public function getPanierFromClient(Request $request, EntityManagerInterface $entityManager){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        $em = $this->getDoctrine()
            ->getManager();
        $panier = $this->getDoctrine()
            ->getRepository(Panier::class)
            ->findByClientId($client->getId());
        $modePaiement = new ModePaiement();
        
        
        $modePaiement = $this->getDoctrine()
            ->getRepository(ModePaiement::class)
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
        $adresses = $this->getDoctrine()
            ->getRepository(Adresse::class)
            ->findByClient($client->getId());

        $adr = new Adresse();

        $formAdresse = $this->createForm(AdresseType::class,$adr);
        
        $formAdresse->handleRequest($request);

        if($formAdresse->isSubmitted() && $formAdresse->isValid()){
            $adr->setClient($this->getUser());
            $entityManager->persist($adr);
            $entityManager->flush();

            return $this->redirectToRoute('commande');
        }


        //dd($adresse);
        return $this->render('commande/index.html.twig' , [
            'controller_name' => 'CommandeController',
            'panier' => $p->getPanier(),
            'adresses' => $adresses,
            'prixTot' => $total,
            'form' => $formAdresse->createView(),
        ]);
    }

   
    /**
     * @Route("/commande/new", name="commande_new")
     */
    public function newCommande(Request $request, MailerService $mailerService, PanierService $panierService){
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $panierService->overwritePanierDBFromSession();
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
            ->findByLibelle("paypal");
        
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
            $quantite = $this->getDoctrine()
            ->getRepository(QuantiteTaille::class)
            ->findQuantiteTailleByArticleAndTaille($lignepanier['article'],$lignepanier['taille']);
            $quantite->setQte($quantite->getQte()-$lignepanier['qte']);
            $em->merge($quantite);
            

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
        $commande->setStatutCommande($statut);
        // tu insere le tableau $LignesDeCommande dans ta commande
        foreach($LignesDeCommande as $ligne){
            $commande->addLigneDeCommande($ligne);
        }
        
        $em->persist($commande);
        $em->flush();

        //modification du stock

        /*
        $quantite = $this->getDoctrine()
        ->getRepository(QuantiteTaille::class)
        ->findQuantiteTailleByArticleAndTaille()
        */

        //vide le panier du client apres avoir valider la commande
        $this->PanierService->clear();

        //Envoie d'un mail de confirmation de commande
        $mailerService->sendOrderConfirmation($commande);
        
        //redirection sur le site paypal
        return $this->redirectToRoute('payment', [
            'commandeId' => $commande->getId()
        ]);
    }
    
        
}
