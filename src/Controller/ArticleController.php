<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;


use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\TypeArticle;
use App\Entity\Section;
use App\Entity\QuantiteTaille;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function index(Request $request)
    {
        $parameters = $request->query->all();
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findByParameters($parameters);
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/article/{id}", name="article_show", requirements={"id"="\d+"})
     */
    public function show($id)
    {
        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);
        return $this->render('article/show.html.twig', [
            'controller_name' => 'ArticleController',
            'article' => $article
        ]);
    }

    /**
     * @Route("/article/new", name="article_new")
     */
    public function new()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $article = new Article();
        $article->setLibelle("Nouvel article");
        $article->setPrixU(0.00);
        $article->setImage("/img/example.png");

        $form = $this->createFormBuilder($article)
            ->add('libelle', TextType::class)
            ->add('description', TextType::class)
            ->add('prix_u', NumberType::class)
            ->add('image', TextType::class)
            ->add('type_article', EntityType::class,[
                'class' => TypeArticle::class,
                'choice_label' => function ($entityType) {
                    return $entityType->getLibelle();
                }
                ])
            ->add('sections', EntityType::class,[
                'class' => Section::class,
                'choice_label' => function ($section) {
                    return $section->getLibelle();
                }
                ])
            /* ->add('quantite_taille', QuantiteTaille::class, EntityType::class,[
                               'class' => TypeArticle::class,
                               'choice_label' => function ($entityType) {
                                   return $entityType->getLibelle();
                               }
                           ])*/
            ->add('save', SubmitType::class, ['label' => 'Creer Article'])
            ->getForm();

        
        return $this->render('article/new.html.twig', [
            'controller_name' => 'ArticleController',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{id}/edit", name="article_edit")
     */
    public function edit($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ArticleFormType::class);

        
    }

    /**
     * @Route("/article/{id}/remove", name="article_remove")
     */
    public function remove($id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em = $this->getDoctrine()
            ->getManager();
        $article = $em->getRepository(Article::class)
            ->find($id);
        $em->remove($article);
        
        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
        ]);
    }
}
