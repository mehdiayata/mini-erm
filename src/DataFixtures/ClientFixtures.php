<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Client;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 0; $i < 6; $i++) {
            $client = new Client();

            $client->setName($faker->name);
            $client->setCountry($faker->country);
            $client->setAdress($faker->address);
            
            $manager->persist($client);
        }

        $manager->flush();
    }
}
