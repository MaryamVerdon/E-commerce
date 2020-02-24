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

    public function newPayment($commande)
    {
        $details = (new Details())
            ->setSubtotal($commande->getPrixTotal());

        $amount = (new Amount())
            ->setTotal($commande->getPrixTotal())
            ->setCurrency('EUR')
            ->setDetails($details);

        $transaction = (new Transaction())
            ->setItemList($this->commandeToItemList($commande))
            ->setDescription("Achat sur FiveSportsWear")
            ->setAmount($amount)
            ->setCustom('test');

        $payment = new Payment();
        $payment->setTransactions([$transaction]);
        $payment->setIntent('sale');
        $redirectUrls = (new RedirectUrls())
            ->setReturnUrl('http://127.0.0.1:8000/pay')
            ->setCancelUrl('http://127.0.0.1:8000/');
        $payment->setRedirectUrls($redirectUrls);
        $payment->setPayer((new Payer())->setPaymentMethod('paypal'));

        $payment->create($this->apiContext);
        return $payment->getApprovalLink();
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