<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commande;
use App\Entity\Adresse;
use App\Form\AdresseType;
use App\Form\EditClientType;

class ClientController extends AbstractController
{
    /**
     * @Route("/compte", name="compte")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        $commandes = $this->getDoctrine()
            ->getRepository(Commande::class)
            ->findLastsByClient($client);
        if($client){ 
            return $this->render('client/index.html.twig', [
                'controller_name' => 'ClientController',
                'client' => $client,
                'commandes' => $commandes,
            ]);
        }
        throw $this->createNotFoundException('Utilisateur null');
    }


    /**
     * @Route("/compte/edit", name="compte_edit")
     */
    public function compteEdit(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        if($client){
            $form = $this->createForm(EditClientType::class, $client);
    
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()){

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($client);
                $entityManager->flush();
    
                return $this->redirectToRoute('compte');
            }

            return $this->render('client/edit.html.twig', [
                'controller_name' => 'ClientController',
                'form' => $form->createView(),
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

    /**
     * @Route("/compte/adresses/new", name="compte_adresse_new")
     */
    public function adresse_new(Request $request, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        if($client){
            $adresse = new Adresse();

            $form = $this->createForm(AdresseType::class, $adresse);
    
            $form->handleRequest($request);
    
            if($form->isSubmitted() && $form->isValid()){
    
                $adresse->setClient($client);
                $entityManager->persist($adresse);
                $entityManager->flush();
    
                return $this->redirectToRoute('compte');
            }

            return $this->render('client/adresse_new.html.twig', [
                'controller_name' => 'ClientController',
                'form' => $form->createView(),
            ]);
        }
        throw $this->createNotFoundException('Utilisateur null');
    }

    /**
     * @Route("/compte/adresses/{id}/edit", name="compte_adresse_edit")
     */
    public function adresse_edit($id, Request $request, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        if($client){
            $adresse = $entityManager->getRepository(Adresse::class)
                ->find($id);
            if($adresse && $adresse->getClient() === $client){
                $form = $this->createForm(AdresseType::class, $adresse);
        
                $form->handleRequest($request);
        
                if($form->isSubmitted() && $form->isValid()){
        
                    $adresse->setClient($client);
                    $entityManager->persist($adresse);
                    $entityManager->flush();
        
                    return $this->redirectToRoute('compte');
                }

                return $this->render('client/adresse_new.html.twig', [
                    'controller_name' => 'ClientController',
                    'form' => $form->createView(),
                ]);
            }
            throw $this->createNotFoundException('Adresse null ou n\'appartenant pas a l\'utilisateur');
        }
        throw $this->createNotFoundException('Utilisateur null');
    }
    
    /**
     * @Route("/compte/adresses/{id}/remove", name="compte_adresse_remove")
     */
    public function adresse_remove($id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        if($client){
            $em = $this->getDoctrine()
                ->getManager();
            $adresse = $em->getRepository(Adresse::class)
                ->find($id);
                
            if($adresse->getClient() == $client){

                $em->remove($adresse);
                $em->flush();

                return $this->redirectToRoute('compte');
            }
        }
        throw $this->createNotFoundException('Utilisateur null');
    }
}
