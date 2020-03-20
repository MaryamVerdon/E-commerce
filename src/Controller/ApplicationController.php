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
        $mostArticlesStocked = $repositoryArticle->findArticlesStocked('DESC');
        $mostArticlesStocked2 = $repositoryArticle->findArticlesStocked('DESC',1,2);
        $sectionsArcicles = $repositoryLigne->findMostSoldArticlesSections();

        return $this->render('application/index.html.twig', [
            'controller_name' => 'ApplicationController',
            'lastArticles' => $lastArticles,
            'mostSoldArticles' => $mostSoldArticles,
            'mostArticlesStocked' => $mostArticlesStocked,
            'mostArticlesStocked2' => $mostArticlesStocked2,
            'sectionsArcicles' => $sectionsArcicles,
        ]);
    }
}
