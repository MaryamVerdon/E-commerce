<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\StatutCommande;

class StatutCommandeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $statutsCommande = [
            "En attente de paiement",
            "Payé",
            "Paiement refusé",
        ];

        foreach($statutsCommande as $sCommande){
            $statutCommande = new StatutCommande();
            $statutCommande->setLibelle($sCommande);
            $manager->persist($statutCommande);
        }

        $manager->flush();
    }
}
