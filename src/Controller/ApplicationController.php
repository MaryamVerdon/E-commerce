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
        $mostSoldArticles = $repository->findMostSoldArticles();
        $lessArticlesStocked = $repository->findLessArticlesStocked();

        return $this->render('application/index.html.twig', [
            'controller_name' => 'ApplicationController',
            'lastArticles' => $lastArticles,
            'mostSoldArticles' => $mostSoldArticles,
            'lessArticlesStocked' => $lessArticlesStocked
        ]);
    }
}
