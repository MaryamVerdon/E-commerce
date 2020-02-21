<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\Panier\PanierService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(SessionInterface $session, PanierService $panierService)
    {
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'panier' => $panierService->getPanier(),
            'total' => $panierService->getTotal(),
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="panier_add")
     */
    public function add($id, Request $request, PanierService $panierService)
    {
        $quantite = $request->query->get('quantite');

        $size = $panierService->add($id,(($quantite && is_numeric($quantite)) ? $quantite : 1));
        
        // return $this->redirectToRoute('article');
        return new JsonResponse(['size' => $size]);
    }

    /**
     * @Route("/panier/remove/{id}", name="panier_remove")
     */
    public function remove($id, PanierService $panierService)
    {
        $panierService->remove($id);

        return $this->redirectToRoute('panier');
    }

    /**
     * @Route("/panier/size", name="panier_size")
     */
    public function size(PanierService $panierService)
    {
        $size = $panierService->getNbArticles();

        return new JsonResponse(['size' => $size]);
    }

}
