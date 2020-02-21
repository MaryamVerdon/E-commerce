<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Article;
use App\Entity\Taille;
use App\Entity\QuantiteTaille;
use App\Form\ArticleType;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/article", name="admin_article")
     */
    public function indexArticle(Request $request)
    {
        $parameters = $request->query->all();
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findByParameters($parameters);
        return $this->render('admin/article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/admin/article/{id}", name="admin_article_show", requirements={"id"="\d+"})
     */
    public function show($id)
    {
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);
        return $this->render('admin/article/show.html.twig', [
            'controller_name' => 'ArticleController',
            'article' => $article
        ]);
    }

    /**
     * @Route("/admin/article/new", name="admin_article_new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            foreach($article->getQuantiteTailles() as $quantite_taille){
                $quantite_taille->setArticle($article);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('admin_article_show', ['id' => $article->getId()]);
        }

        
        return $this->render('admin/article/new.html.twig', [
            'controller_name' => 'ArticleController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article/{id}/edit", name="admin_article_edit")
     */
    public function edit($id, Request $request, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (null === $article = $entityManager->getRepository(Article::class)->find($id)) {
            throw $this->createNotFoundException('Aucun article pour l \'id '.$id);
        }

        $originalQuantiteTailles = new ArrayCollection();

        foreach($article->getQuantiteTailles() as $quantite_taille){
            $originalQuantiteTailles->add($quantite_taille);
        }

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            foreach($originalQuantiteTailles as $quantite_taille){
                if(false === $article->getQuantiteTailles()->contains($quantite_taille)){
                    // Remove quantitetaille
                    $entityManager->remove($quantite_taille);
                }
            }
            foreach($article->getQuantiteTailles() as $quantite_taille){
                $quantite_taille->setArticle($article);
            }
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('admin_article_show', ['id' => $article->getId()]);
        }

        
        return $this->render('admin/article/new.html.twig', [
            'controller_name' => 'ArticleController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/article/{id}/remove", name="admin_article_remove")
     */
    public function remove($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $this->getDoctrine()
            ->getManager();
        $article = $em->getRepository(Article::class)
            ->find($id);
        $em->remove($article);
        
        return $this->redirectToRoute('admin_article');
    }
}
