<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Employee;
use App\Repository\CompanyRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EmployeeFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private CompanyRepository $companyRepository){}
   
    public function load(ObjectManager $manager): void
    {
        // Permet de générer des données aléatoire
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $employee = new Employee();

            $employee->setName($faker->name);
            $employee->setBirthday($faker->dateTimeBetween($startDate = '-21 years', $endDate = 'now', $timezone = null));
            $employee->setCountry($faker->country);
            $employee->setFirstDay($faker->dateTimeBetween($startDate = '-7 years', $endDate = 'now', $timezone = null));


           // Définit les employés à une compagnie
            $companys = $this->companyRepository->findAll();
            $employee->setCompany($companys[$faker->numberBetween(0, count($companys) - 1)]);

            $manager->persist($employee);
        }

        $manager->flush();
    }

    
    
    public function getDependencies() {
        return [
            CompanyFixtures::class
        ];
    
    }
}
