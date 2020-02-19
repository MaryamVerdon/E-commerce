<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Section;

class SectionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $sections = [
            "Homme",
            "Femme",
            "Enfant"
        ];

        foreach($sections as $sec){
            $section = new Section();
            $section->setLibelle($sec);
            $manager->persist($section);
        }

        $manager->flush();
    }
}
