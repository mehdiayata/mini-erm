<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class UserFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->passwordHasher = $userPasswordHasher;
    }


    public function load(ObjectManager $manager)
    {
        // Permet de générer des données aléatoire
        $faker = Faker\Factory::create('fr_FR');

        
        for ($i = 0; $i < 3; $i++) {
            $user = new User();

            $user->setEmail($faker->email);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'minierp'));

            $manager->persist($user);

            $manager->flush();
        }
    }
}
