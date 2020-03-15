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
        $lastArticles = $repositoryArticle->findLastArticles(8);
        $mostSoldArticles = $repositoryLigne->findMostSoldArticles(8);
        $lessArticlesStocked = $repositoryArticle->findArticlesStocked();
        $mostArticlesStocked = $repositoryArticle->findArticlesStocked('DESC',2);

        // dd($lastArticles,$mostSoldArticles,$lessArticlesStocked);

        $test = $repositoryLigne->findMostSoldArticlesSections();

        dd($test);

        return $this->render('application/index.html.twig', [
            'controller_name' => 'ApplicationController',
            'lastArticles' => $lastArticles,
            'mostSoldArticles' => $mostSoldArticles,
            'lessArticlesStocked' => $lessArticlesStocked,
            'mostArticlesStocked' => $mostArticlesStocked,
            'sectionsArcicles' => [],
        ]);
    }
}
