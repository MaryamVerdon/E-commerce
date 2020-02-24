<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Payment\PaymentService;
use App\Entity\Commande;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment", name="payment")
     */
    public function index(PaymentService $paymentService)
    {
        $commande = $this->getDoctrine()
            ->getRepository(Commande::class)
            ->findOneBy([]);

        $payment = $paymentService->newPayment($commande);
        echo($payment->getApprovalLink());

        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
}
