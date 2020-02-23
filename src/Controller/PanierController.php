<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\Panier\PanierService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Article;

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
        $idTaille = $request->query->get('taille');
        $quantite = $request->query->get('quantite');
        $quantite = (($quantite && is_numeric($quantite)) ? $quantite : 1);

        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        foreach($article->getQuantiteTailles() as $quantiteTaille){
            if($quantiteTaille->getTaille()->getId() == $idTaille){
                if($quantiteTaille->getQte() < ($panierService->getQteFor($id, $idTaille) + $quantite)){
                    return new Response('Pas assez de quantité pour l\'article',282);
                }
            }
        }

        $size = $panierService->add($id, $idTaille, $quantite);

        // return $this->redirectToRoute('article');
        return new JsonResponse(['size' => $size]);
    }

        /**
     * @Route("/panier/modify/{id}", name="panier_modify")
     */
    public function modify($id, Request $request, PanierService $panierService)
    {
        $idTaille = $request->query->get('taille');
        $quantite = $request->query->get('quantite');
        $quantite = (($quantite && is_numeric($quantite)) ? $quantite : 1);

        $size = $panierService->modify($id, $idTaille, $quantite);

        // return $this->redirectToRoute('article');
        return $this->redirectToRoute('panier');
    }

    /**
     * @Route("/panier/remove/{id}", name="panier_remove")
     */
    public function remove($id, Request $request, PanierService $panierService)
    {
        $idTaille = $request->query->get('taille');
        $panierService->remove($id, $idTaille);

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

    /**
     * @Route("/panier/test", name="panier_test")
     */
    public function test(PanierService $panierService)
    {
        $panier = $panierService->getPanierTest();

        dd($panier);
    }
}
