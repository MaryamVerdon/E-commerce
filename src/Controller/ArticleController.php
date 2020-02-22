<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Entity\Taille;
use App\Entity\QuantiteTaille;
use App\Form\ArticleType;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article", name="article")
     */
    public function index(Request $request)
    {
        $parameters = $request->query->all();

        $page = 1;
        if(isset($parameters['page'])){
            $page = $parameters['page'];
        }

        $nbMaxParPage = 20;
        if(isset($parameters['nb_max_par_page'])){
            $nbMaxParPage = $parameters['nb_max_par_page'];
        }

        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findByParametersPagine($page, $nbMaxParPage, $parameters);

        $indexParam = 0;
        $parametres = count($parameters) > 0 ? "?" : "";
        foreach($parameters as $param => $value ){
            if($param != 'page'){
                $parametres .= ($indexParam > 0 ? "&" : "") . $param . "=" . $value;
                $indexParam++;
            }
        }

        $pagination = [
            'page' => $page,
            'nbPages' => (ceil(count($articles) / $nbMaxParPage)),
            'params' => $parametres,
        ];

        return $this->render('article/index.html.twig', [
            'controller_name' => 'ArticleController',
            'articles' => $articles,
            'pagination' => $pagination,
        ]);
    }

    /*
     * @Route("/article", name="article")
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
     */

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
}
