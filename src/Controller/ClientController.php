<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    /**
     * @Route("/compte", name="compte")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if($user){
            return $this->render('client/index.html.twig', [
                'controller_name' => 'ClientController',
                'user' => $user
            ]);
        }
        throw $this->createNotFoundException('Utilisateur null');
    }
}
