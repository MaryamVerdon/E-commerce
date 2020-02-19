<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Entity\Section;

class SectionController extends AbstractController
{
    /**
     * @Route("/section", name="section")
     */
    public function index()
    {
        return $this->render('section/index.html.twig', [
            'controller_name' => 'SectionController',
        ]);
    }

    /**
     * @Route("/section/{libelle}", name="section")
     */
    public function show($libelle)
    {
        $section = $this->getDoctrine()
            ->getRepository(Section::class)
            ->findBy(['libelle' => $libelle]);

        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findBySection($section[0]);
        
        return $this->render('section/index.html.twig', [
            'controller_name' => 'SectionController',
            'articles' => $articles
        ]);
    }
}
