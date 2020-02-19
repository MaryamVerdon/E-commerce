<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\TypeArticle;
use App\Entity\Section;
use App\Entity\Article;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{

    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }

    public function load(ObjectManager $manager)
    {
        $typesArticles = $this->entityManager
            ->getRepository(TypeArticle::class)
            ->findAll();

        $sections = $this->entityManager
            ->getRepository(Section::class)
            ->findAll();

        $tailles = [
            "S",
            "M",
            "L",
            "XL",
            "XXL"
        ];

        foreach($typesArticles as $typeArticle){
            foreach($sections as $section){
                $max = mt_rand(1,4);
                for($i = 0; $i < $max;$i++){
                    $article = new Article();
                    $article->setLibelle($typeArticle->getLibelle() . " - " . $section->getLibelle());
                    $article->setDescription($typeArticle->getLibelle() . " pour " . $section->getLibelle());
                    $article->setPrixU(mt_rand(100, 500) / 10);
                    $article->setTaille($tailles[array_rand($tailles)]);
                    $article->setQteStock(mt_rand(0,20));
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

        $manager->flush();
    }


    public function getDependencies()
    {
        return array(
            TypeArticleFixtures::class,
            SectionFixtures::class,
        );
    }
}
