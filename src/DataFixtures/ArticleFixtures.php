<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\TypeArticle;
use App\Entity\Section;
use App\Entity\Article;
use App\Entity\Taille;
use App\Entity\QuantiteTaille;
use App\Entity\Categorie;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{

    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }

    public function load(ObjectManager $manager)
    {

        $data = $this->getFromJson();

        $sections = [];
        $tailles = [];

        foreach($data["tailles"] as $tai){
            $taille = new Taille();
            $taille->setLibelle($tai);
            $manager->persist($taille);
            $tailles[] = $taille;
        }

        foreach($data["sections"] as $sec){
            $section = new Section();
            $section->setLibelle($sec);
            $manager->persist($section);
            $sections[] = $section;
        }

        foreach($data['categories'] as $cat){
            $categorie = new Categorie();
            $categorie->setLibelle($cat);
            foreach($data['types-articles'] as $typeArt){
                $tabTypeArt = explode(".",$typeArt);
                if($tabTypeArt[0] === $cat){
                    $typeArticle = new TypeArticle();
                    $typeArticle->setLibelle($tabTypeArt[1]);
                    $typeArticle->setCategorie($categorie);
                    foreach($data['articles']->articles as $art){
                        $ar = get_object_vars($art);
                        if($ar['type-article'] === $tabTypeArt[1]){
                            $article = new Article();
                            $article->setLibelle($ar['libelle']);
                            $article->setDescription($ar['description']);
                            $article->setPrixU($ar['prix_u']);
                            $article->setImage($ar['image']);

                            foreach($sections as $sec){
                                foreach($ar['sections'] as $arSec){
                                    if($arSec === $sec->getLibelle()){
                                        $article->addSection($sec);
                                    }
                                }
                            }

                            $article->setTypeArticle($typeArticle);
                            
                            foreach($ar['tailles'] as $tai){
                                foreach($tailles as $tail){
                                    if($tai === $tail->getLibelle()){
                                        $quantiteTaille = new QuantiteTaille();
                                        $quantiteTaille->setArticle($article);
                                        $quantiteTaille->setTaille($tail);
                                        $quantiteTaille->setQte(mt_rand(0,20));
                                        $manager->persist($quantiteTaille);
                                    }
                                }
                            }
                            $manager->persist($article);
                        }
                    }
                    $manager->persist($typeArticle);
                }
            }
            $manager->persist($categorie);
        }

        /*
        $articles = json_decode(file_get_contents(__DIR__.'/../../articles.json'));

        $typeArticles = [];
        $sections = [];
        $tailles = [];

        foreach($articles as $article){

        }
        $typesArticles = $this->entityManager
            ->getRepository(TypeArticle::class)
            ->findAll();

        $sections = $this->entityManager
            ->getRepository(Section::class)
            ->findAll();

        $tailles = $this->entityManager
            ->getRepository(Taille::class)
            ->findAll();


        foreach($typesArticles as $typeArticle){
            foreach($sections as $section){
                $max = mt_rand(1,4);
                for($i = 0; $i < $max;$i++){
                    $article = new Article();
                    $article->setLibelle($typeArticle->getLibelle() . " - " . $section->getLibelle());
                    $article->setDescription($typeArticle->getLibelle() . " pour " . $section->getLibelle());
                    $article->setPrixU(mt_rand(1000, 5000) / 100);
                    foreach($tailles as $taille){
                        $randTaille = mt_rand(0,4);
                        if($randTaille > 0){
                            $quantiteTaille = new QuantiteTaille();
                            $quantiteTaille->setArticle($article);
                            $quantiteTaille->setTaille($taille);
                            $quantiteTaille->setQte(mt_rand(0,20));
                            $manager->persist($quantiteTaille);
                        }
                    }
                    $article->setImage("/img/example.png");
                    $article->setTypeArticle($typeArticle);
                    $article->addSection($section);
                    if(mt_rand(0,3)>2){
                        $article->addSection($sections[array_rand($sections)]);
                    }
                    $manager->persist($article);
                }
            }
        }
        */

        $manager->flush();
    }

    private function getFromJson(){
        $articles = json_decode(file_get_contents(__DIR__.'/../../articles.json'));

        $categories = [];
        $typeArticles = [];
        $sections = [];
        $tailles = [];

        foreach($articles->articles as $article){
            $art = get_object_vars($article);
            $categories[] = $art["categorie"];
            $typeArticles[] = $art["categorie"].".".$art["type-article"];
            foreach($art["sections"] as $section){
                $sections[] = $section;
            }
            foreach($art["tailles"] as $taille){
                $tailles[] = $taille;
            }
        }
        
        $categories = array_unique($categories);
        $typeArticles = array_unique($typeArticles);
        $sections = array_unique($sections);
        $tailles = array_unique($tailles);

        return ['articles' => $articles, 'categories' => $categories, 'types-articles' => $typeArticles, 'sections' => $sections, 'tailles' => $tailles];
    }

    public function getDependencies()
    {
        return array(
            TypeArticleFixtures::class,
            SectionFixtures::class,
            TailleFixtures::class,
        );
    }
}
