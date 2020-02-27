<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Commande;

class ClientController extends AbstractController
{
    /**
     * @Route("/compte", name="compte")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        if($client){
            return $this->render('client/index.html.twig', [
                'controller_name' => 'ClientController',
                'user' => $client
            ]);
        }
        throw $this->createNotFoundException('Utilisateur null');
    }

    /**
     * @Route("/compte/commandes", name="compte_commandes")
     */
    public function commandes()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        if($client){
            
            $commandes = $this->getDoctrine()
                ->getRepository(Commande::class)
                ->findByClient($client);
                // dd($commandes);
            return $this->render('client/commandes.html.twig', [
                'controller_name' => 'ClientController',
                'commandes' => $commandes
            ]);
        }
        throw $this->createNotFoundException('Utilisateur null');
    }

    /**
     * @Route("/compte/commandes/{id}", name="compte_commande_show")
     */
    public function commandes_show($id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        if($client){
            $commande = $this->getDoctrine()
                ->getRepository(Commande::class)
                ->find($id);
                
            if($commande->getClient() == $client){
                return $this->render('client/commande_show.html.twig', [
                    'controller_name' => 'ClientController',
                    'commande' => $commande
                ]);
            }
        }
        throw $this->createNotFoundException('Utilisateur null');
    }
}
