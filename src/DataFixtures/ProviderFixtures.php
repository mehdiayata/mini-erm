<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Provider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProviderFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 0; $i < 6; $i++) {
            $provider = new Provider();

            $provider->setName($faker->word);
            $provider->setCountry($faker->country);
            $provider->setAdress($faker->address);
            
            $manager->persist($provider);
        }




        $manager->flush();
    }
}
