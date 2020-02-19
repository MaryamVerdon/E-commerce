<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Categorie;
use App\Entity\TypeArticle;

class TypeArticleFixtures extends Fixture implements DependentFixtureInterface
{

    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }

    public function load(ObjectManager $manager)
    {
        $categories = $this->entityManager
            ->getRepository(Categorie::class)
            ->findAll();

        $vetementsTypeA = [
            "Pull",
            "T-shirt",
            "Jupe",
            "Pantalon",
            "Veste"
        ];

        $chaussuresTypeA = [
            "Talon",
            "Basket",
            "Ville"
        ];

        $accessoireType1 = [
            "Ceinture",
            "Casquette",
            "Echarpe"
        ];

        foreach($categories as $categorie){
            $listAccessoires = [];
            switch($categorie->getLibelle()){
                case "Vetement":
                    $listAccessoires = $this->createListAccessoires($vetementsTypeA, $categorie);
                    break;
                case "Chaussure":
                    $listAccessoires = $this->createListAccessoires($chaussuresTypeA, $categorie);
                    break;
                case "Accessoire":
                    $listAccessoires = $this->createListAccessoires($accessoireType1, $categorie);
                    break;
            }
            foreach($listAccessoires as $accessoire){
                $manager->persist($accessoire);
            }
        }

        $manager->flush();
    }

    private function createListAccessoires($tabTypeA, $categorie)
    {
        $list = [];
        foreach($tabTypeA as $typeA){
            $typeArticle = new TypeArticle();
            $typeArticle->setLibelle($typeA);
            $typeArticle->setCategorie($categorie);
            array_push($list, $typeArticle);
        }
        return $list;
    }

    public function getDependencies()
    {
        return array(
            CategorieFixtures::class
        );
    }
}
