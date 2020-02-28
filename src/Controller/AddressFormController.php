<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Form\AddressFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class AddressFormController extends AbstractController
{
    /**
     * @Route("/address", name="address_form")
     */
    public function registration(Request $request)
    {
        $adresse = new Adresse();
        $form = $this->createForm(AddressFormType::class, $adresse);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($adresse);
            $em->flush();
        }
        return $this->render('address_form/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
