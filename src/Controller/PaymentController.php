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
                dd($paymentUrl);
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $client = $this->getUser();
        if($client){
            $em = $this->getDoctrine()
                ->getManager();
            $commande = $em->getRepository(Commande::class)
                ->find($id);
            if($commande){

                $paymentId = $request->query->get("paymentId");
                $payerId = $request->query->get("PayerID");
                if($payerId && $paymentId){
                    $success = $paymentService->success($paymentId, $payerId);
                    if($success[0] != true){
                        throw $this->createNotFoundException('Echec du paiement ' . $success[0]);
                    }
                    $statusCommande = $em->getRepository(StatutCommande::class)
                        ->findByCode(2);

                    $commande->setStatutCommande($statusCommande);

                    $em->persist($commande);
                    $em->flush();

                    $payment = $success[1];
                    $transaction = $payment->transactions[0];
                    //dd($transaction->getAmount());
                    $resPayment = [
                        'paymentId' => $payment->id,
                        'paymentMode' => $payment->payer->payment_method,
                        'state' => $payment->state,
                        'address' => $payment->payer->payer_info->shipping_address,
                        'price' => $transaction->getAmount()->getTotal(),
                        'description' => $transaction->getDescription(),
                        'user' => ($payment->payer->payer_info->first_name . " " . $payment->payer->payer_info->last_name),
                    ];
                    /*
                    "recipient_name" => "John Doe"
                    "line1" => "Av. de la Pelouse"
                    "city" => "Paris"
                    "state" => "Alsace"
                    "postal_code" => "75002"
                    "country_code" => "FR"
                    */
                    // dd($resPayment);

                    return $this->render('payment/index.html.twig', [
                        'controller_name' => 'PaymentController',
                        'payment' => $resPayment,
                        'commande' => $commande,
                    ]);
                }
                throw $this->createNotFoundException('Erreur dans la requette');
            }
            throw $this->createNotFoundException('Commande null');
        }
        //Exception
        throw $this->createNotFoundException('Client null');
    }
}
