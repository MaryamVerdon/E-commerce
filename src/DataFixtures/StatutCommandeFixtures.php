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
            1 => "En attente de paiement",
            2 => "Payé",
            3 => "En préparation",
            3 => "En cours de livraison",
            4 => "Livré",
            9 => "Paiement refusé",
        ];

        foreach($statutsCommande as $sCode => $sCommande){
            $statutCommande = new StatutCommande();
            $statutCommande->setLibelle($sCommande);
            $statutCommande->setCode($sCode);
            $manager->persist($statutCommande);
        }

        $manager->flush();
    }
}
