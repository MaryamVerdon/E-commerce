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
    public function index(PaymentService $paymentService)
    {
        $client = $this->getUser();
        if($client){
            $commande = $this->getDoctrine()
                ->getRepository(Commande::class)
                ->findLastByClient($client);
                if($commande){

                    $paymentUrl = $paymentService->newPayment($commande);

                    if(isset($paymentUrl['message'])){
                        dd($paymentUrl['message']);
                        // Exception
                    }

                    return $this->redirect($paymentUrl);
                }
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
        $client = $this->getUser();
        if($client){
            $commande = $this->getDoctrine()
                ->getRepository(Commande::class)
                ->findLastByClient($client);
            if($commande){
                $paymentId = $request->query->get("paymentId");
                $payerId = $request->query->get("payerId");

                $success = $paymentService->success($commande, $paymentId, $payerId);
                if($success != true){
                    dd($success);
                    // new Exception
                }
                return $this->render('payment/index.html.twig', [
                    'controller_name' => 'PaymentController',
                ]);
            }
        }
        //Exception
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }


    private function getCommande(){
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();
            $commande = new Commande();

            $nbLigne = mt_rand(1,10);
            for($x = 0; $x < $nbLigne; $x++){
                $ligneDeCommande = new LigneDeCommande();
                $ligneDeCommande->setQte(mt_rand(1,10));
                $ligneDeCommande->setArticle($articles[array_rand($articles)]);
                $commande->addLigneDeCommande($ligneDeCommande);
            }
        return $commande;
    }
}
