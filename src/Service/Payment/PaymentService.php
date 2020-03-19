<?php

namespace App\Service\Payment;

require __DIR__.'/../../../vendor/autoload.php';

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;
use PayPal\Exception\PayPalConnectionException;
use App\Entity\Commande;
use App\Entity\LigneDeCommande;
use App\Entity\Article;

class PaymentService {

    protected $apiContext;

    public function __construct()
    {
        $ids = require(__DIR__.'/../../../paypal.php');
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $ids['id'],
                $ids['secret']
            )
        );
    }

    private function newTransaction($commande){
        $details = (new Details())
            ->setSubtotal($commande->getPrixTotal())
            ->setShipping($commande->getFraisDePort());

        $amount = (new Amount())
            ->setTotal($commande->getPrixTotalWithShipping())
            ->setCurrency('EUR')
            ->setDetails($details);

        return (new Transaction())
            ->setItemList($this->commandeToItemList($commande))
            ->setDescription("Achat sur FiveSportsWear")
            ->setAmount($amount)
            ->setCustom('test');
    }

    public function newPayment($commande)
    {
        $transaction = $this->newTransaction($commande);
        $payment = new Payment();
        $payment->setTransactions([$transaction]);
        $payment->setIntent('sale');
        $redirectUrls = (new RedirectUrls())
            ->setReturnUrl('http://127.0.0.1:8000/payment/success/' . $commande->getId())
            ->setCancelUrl('http://127.0.0.1:8000/');
        $payment->setRedirectUrls($redirectUrls);
        $payment->setPayer((new Payer())->setPaymentMethod('paypal'));

        try {
            $payment->create($this->apiContext);
        }catch (PayPalConnectionException $e){
            return ['message' => $e->getMessage()];
        }
        return $payment->getApprovalLink();
    }

    public function success($paymentId, $payerId){
        $payment = Payment::get($paymentId, $this->apiContext);

        // $transaction = $this->newTransaction($commande);
        $execution = (new PaymentExecution())
            ->setPayerId($payerId);
            // ->addTransaction($transaction);
        try{
            $result = $payment->execute($execution, $this->apiContext);
            return [true, $result];
        }catch (PayPalConnectionException $e){
            return [false, $e->getMessage()];
        }
    }

    private function commandeToItemList($commande)
    {
        $list = new ItemList();
        foreach($commande->getLignesDeCommande() as $ligneDeCommande){
            $item = new Item();
            $item->setName($ligneDeCommande->getArticle()->getLibelle());
            $item->setPrice($ligneDeCommande->getArticle()->getPrixU());
            $item->setCurrency('EUR');
            $item->setQuantity($ligneDeCommande->getQte());
            $list->addItem($item);
        }
        return $list;
    }

}