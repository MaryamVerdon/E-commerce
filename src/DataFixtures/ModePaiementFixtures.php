<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\ModePaiement;

class ModePaiementFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $modesPaiement = [
            "CB",
            "PayPal",
            "Virement",
        ];

        foreach($modesPaiement as $mPaiement){
            $modePaiement = new ModePaiement();
            $modePaiement->setLibelle($mPaiement);
            $manager->persist($modePaiement);
        }
        

        $manager->flush();
    }
}
