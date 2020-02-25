<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\LigneDeCommande;
use App\Entity\ModePaiement;
use App\Entity\Client;
use App\Entity\Article;
use App\Entity\Commande;
use App\Entity\Taille;
use App\Entity\StatutCommande;
use Faker\Factory;

class CommandeFixtures extends Fixture implements DependentFixtureInterface
{

    private $entityManager;
    private $faker;
    
    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
        
        $articles = $this->entityManager
            ->getRepository(Article::class)
            ->findAll();
        
        $tailles = $this->entityManager
            ->getRepository(Taille::class)
            ->findAll();

        $clients = $this->entityManager
            ->getRepository(Client::class)
            ->findAll();

        $modesPaiement = $this->entityManager
            ->getRepository(ModePaiement::class)
            ->findAll();

        $statutsCommande = $this->entityManager
            ->getRepository(StatutCommande::class)
            ->findAll();

        for($i = 0; $i < 30; $i++){
            $commande = new Commande();
            $commande->setDate($this->faker->dateTimeBetween($startDate = '-5 years', $endDate = 'now', $timezone = null));

            $nbLigne = mt_rand(1,10);
            for($x = 0; $x < $nbLigne; $x++){
                $ligneDeCommande = new LigneDeCommande();
                $ligneDeCommande->setQte(mt_rand(1,10));
                $ligneDeCommande->setArticle($articles[array_rand($articles)]);
                $ligneDeCommande->setTaille($tailles[array_rand($tailles)]);
                
                $commande->addLigneDeCommande($ligneDeCommande);
            }

            $client = new Client();

            while(!$client->getAdresses() || ($client->getAdresses() && count($client->getAdresses()) < 1)){
                $client = $clients[array_rand($clients)];
            }

            $commande->setClient($client);
            $commande->setModePaiement($modesPaiement[array_rand($modesPaiement)]);
            $commande->setStatutCommande($statutsCommande[array_rand($statutsCommande)]);
            $commande->setAdresse($client->getAdresses()[0]);
            $commande->setFraisDePort(mt_rand(100,500)/100);

            $manager->persist($commande);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            ArticleFixtures::class,
            ModePaiementFixtures::class,
            ClientFixtures::class,
            StatutCommandeFixtures::class,
        );
    }
}
