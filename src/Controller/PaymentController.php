<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
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
        //$commande = $this->getDoctrine()
        //    ->getRepository(Commande::class)
        //    ->findOneBy([]);

        $commande = $this->getCommande();

        $paymentUrl = $paymentService->newPayment($commande);

        return $this->redirect($paymentUrl);
        /*
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
        */
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
