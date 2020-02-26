<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Payment\PaymentService;
use App\Entity\Commande;


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
     * @Route("/payment/success", name="payment_success")
     */
    public function success(Request $request, PaymentService $paymentService)
    {
        // $client = $this->getUser();
        // if($client){
        // $commande = $this->getDoctrine()
        //    ->getRepository(Commande::class)
        //    ->findLastByClient($client);
        // if($commande){
        $paymentId = $request->query->get("paymentId");
        $payerId = $request->query->get("payerId");

        $success = $paymentService->success($paymentId, $payerId);
        if($success != true){
            dd($success);
            // new Exception
        }
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
            // }
        // }
        //Exception
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
}
