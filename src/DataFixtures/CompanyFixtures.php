<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CompanyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 4; $i++) {
            $company = new Company();

            $company->setName($faker->word);
            $company->setBalance($faker->randomFloat(25000, 300000));
            $company->setCountry($faker->country);

            $manager->persist($company);
        }

        $manager->flush();
    }
}
