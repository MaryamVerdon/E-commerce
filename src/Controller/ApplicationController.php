<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\LigneDeCommande;
use App\Repository\ArticleRepository;

class ApplicationController extends AbstractController
{
    /**
     * @Route("/", name="application")
     */
    public function index()
    {
        $repositoryArticle = $this->getDoctrine()->getRepository(Article::class);
        $repositoryLigne = $this->getDoctrine()->getRepository(LigneDeCommande::class);
        $lastArticles = $repositoryArticle->findLastArticles();
        $mostSoldArticles = $repositoryLigne->findMostSoldArticles();
        $lessArticlesStocked = $repositoryArticle->findLessArticlesStocked();

        return $this->render('application/index.html.twig', [
            'controller_name' => 'ApplicationController',
            'lastArticles' => $lastArticles,
            'mostSoldArticles' => $mostSoldArticles,
            'lessArticlesStocked' => $lessArticlesStocked
        ]);
    }
}
