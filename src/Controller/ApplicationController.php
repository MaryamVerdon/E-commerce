<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Repository\ArticleRepository;

class ApplicationController extends AbstractController
{
    /**
     * @Route("/", name="application")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Article::class);
        $lastArticles = $repository->findLastArticles();
<<<<<<< HEAD
        $mostSoldArticles = $repository->findMostSoldArticles();
        $lessArticlesStocked = $repository->findLessArticlesStocked();

        return $this->render('application/index.html.twig', [
            'controller_name' => 'ApplicationController',
            'lastArticles' => $lastArticles,
            'mostSoldArticles' => $mostSoldArticles,
            'lessArticlesStocked' => $lessArticlesStocked
=======

        return $this->render('application/index.html.twig', [
            'controller_name' => 'ApplicationController',
            'lastArticle' => $lastArticles[0]
>>>>>>> ecc5a03db70dd218c06a87c8fbfe3ab079e6c45c
        ]);
    }
}
