<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Taille;

class TailleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        
        $tailles = [
            "S",
            "M",
            "L",
            "XL",
            "XXL"
        ];

        foreach($tailles as $t){
            $taille = new Taille();
            $taille->setLibelle($t);
            $manager->persist($taille);
        }

        $manager->flush();
    }
}
