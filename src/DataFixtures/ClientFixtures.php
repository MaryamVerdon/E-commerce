<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Client;
use App\Entity\Adresse;
use Faker\Factory;

class ClientFixtures extends Fixture
{

    private $faker;
    private $passwordEncoder;
    
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->passwordEncoder = $encoder;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
        $maxCli = 19;
        for($i = 0; $i < $maxCli; $i++){
            $client = new Client();
            $client->setPrenom($this->faker->firstName);
            $client->setNom($this->faker->lastName);
            $client->setEmail(mb_strtolower($client->getPrenom() . "." . $client->getNom()) . "@gmail.com");
            $client->setPassword($this->passwordEncoder->encodePassword($client,"azerty"));
            $client->setRoles(['ROLE_USER']);
            $client->setConfirmationToken(null);
            $client->setActif(true);

            $max = rand(0,4) > 0 ? (rand(0,4) > 0 ? 1 : 2) : 0; 
            for($x = 0; $x < $max; $x++){
                $adresse = new Adresse();
                $adresse->setAdresse($this->faker->streetAddress);
                $adresse->setVille($this->faker->city);
                $adresse->setCp($this->faker->postcode);
                $adresse->setPays("France");
                $adresse->setTel($this->faker->phoneNumber);

                $adresse->setClient($client);
                $manager->persist($adresse);
            }

            $manager->persist($client);

            // echo("Client " . $i+1 . " sur " . $maxCli+1 . " créé\n");
        }

        $client = new Client();
        $client->setPrenom($this->faker->firstName);
        $client->setNom($this->faker->lastName);
        $client->setEmail("admin@gmail.com");
        $client->setPassword($this->passwordEncoder->encodePassword($client,"azerty"));
        $client->setRoles(['ROLE_ADMIN']);
        $client->setConfirmationToken(null);
        $client->setActif(true);

        $manager->persist($client);

        $manager->flush();
    }
}
