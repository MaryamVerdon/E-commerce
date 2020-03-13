<?php

namespace App\Service\Mailer;

use App\Entity\Commande;

class MailerService {

    private $from = 'monsuperprojetiut@gmail.com';
    private $mailer;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, \Twig\Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function sendOrderConfirmation(Commande $commande){
        $message = (new \Swift_Message('Confirmation Commande'))
            ->setFrom($this->from)
            ->setTo($commande->getClient()->getEmail())
            ->setBody(
                $this->templating->render(
                    'email/commande-confirmation.html.twig',
                    ['commande' => $commande]
                ),
                'text/html'
            );

        return $this->mailer->send($message);
    }

    public function sendRegisteringConformation(Client $client){
        $message = (new \Swift_Message('Confirmez votre compte'))
            ->setFrom($this->from)
            ->setTo($client->getEmail())
            ->setBody(
                $this->templating->render(
                    'email/inscription-confirmation.html.twig',
                    [
                        'prenom' => $user->getPrenom(),
                        'token' => $user->getConfirmationToken()
                    ]
                ),
                'text/html'
            );

        return $this->mailer->send($message);
    }

}
