<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Categorie;

class CategorieFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        /*
        $categories = [
            "Vetement",
            "Chaussure",
            "Accessoire"
        ];

        foreach($categories as $cat){
            $categorie = new Categorie();
            $categorie->setLibelle($cat);
            $manager->persist($categorie);
        }

        $manager->flush();
        */
    }
}
