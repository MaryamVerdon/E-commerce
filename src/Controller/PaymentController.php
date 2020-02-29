<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Payment\PaymentService;
use App\Entity\Commande;
use App\Entity\StatutCommande;


use App\Entity\Article;
use App\Entity\LigneDeCommande;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment", name="payment")
     */
    public function index(Request $request, PaymentService $paymentService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        $commandeId = $request->query->get("commandeId");
        $commande = $this->getDoctrine()
            ->getRepository(Commande::class)
            ->find($commandeId);
        if($commande && $commande->getClient() == $client){

            $paymentUrl = $paymentService->newPayment($commande);

            if(isset($paymentUrl['message'])){
                dd($paymentUrl['message']);
                // Exception
            }

            return $this->redirect($paymentUrl);
        }
        
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
        
    }

    /**
     * @Route("/payment/success/{id}", name="payment_success")
     */
    public function success($id, Request $request, PaymentService $paymentService)
    {
        // $client = $this->getUser();
        // if($client){
        // $commande = $this->getDoctrine()
        //    ->getRepository(Commande::class)
        //    ->findLastByClient($client);
        // if($commande){
        $client = $this->getUser();
        if($client){
            $em = $this->getDoctrine()
                ->getManager();
            $commande = $em->getRepository(Commande::class)
                ->find($id);
            if($commande){

                $paymentId = $request->query->get("paymentId");
                $payerId = $request->query->get("payerId");

                $success = $paymentService->success($paymentId, $payerId);
                if($success != true){
                    throw $this->createNotFoundException('Echec du paiement');
                }
                $statusCommande = $em->getRepository(StatutCommande::class)
                    ->findByCode(2);

                $commande->setStatutCommande($statusCommande);

                $em->persist($commande);
                $em->flush();

                return $this->render('payment/index.html.twig', [
                    'controller_name' => 'PaymentController',
                ]);
            }
        }
        //Exception
        throw $this->createNotFoundException('client ou commande null');
    }
}
