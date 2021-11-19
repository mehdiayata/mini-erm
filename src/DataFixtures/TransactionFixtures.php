<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Transaction;
use App\DataFixtures\ClientFixtures;
use App\Repository\ClientRepository;
use App\DataFixtures\CompanyFixtures;
use App\DataFixtures\ProductFixtures;
use App\Repository\CompanyRepository;
use App\Repository\ProductRepository;
use App\DataFixtures\EmployeeFixtures;
use App\DataFixtures\ProviderFixtures;
use App\Repository\EmployeeRepository;
use App\Repository\ProviderRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
        
    public function __construct(private ClientRepository $clientRepository, private CompanyRepository $companyRepository, 
    private ProductRepository $productRepository, private ProviderRepository $providerRepository, 
    private EmployeeRepository $employeeRepository) {}

    public function load(ObjectManager $manager): void
    {
        // Permet de générer des données aléatoire
        $faker = Faker\Factory::create('fr_FR');

        // Récupère les données à transmettre (compagnie, client ...)
        $clients = $this->clientRepository->findAll();
        $companys = $this->companyRepository->findAll();
        $products = $this->productRepository->findAll();
        $providers = $this->providerRepository->findAll();
        $employees = $this->employeeRepository->findAll();

        for($i = 0; $i < 100; $i++){
            $transaction = new Transaction;

            $transaction->setClient($clients[$faker->numberBetween(0, count($clients) - 1)]);

            
            $transaction->setProduct($products[$faker->numberBetween(0, count($products) - (count($products) / 3))]);
            
            if($transaction->getProduct()->getCompany()) {
                $transaction->setCompany($transaction->getProduct()->getCompany());
            }

            if($transaction->getProduct()->getProvider()) {
                $transaction->setProvider($transaction->getProduct()->getProvider());
            }

            $transaction->setEmployee($employees[$faker->numberBetween(0, count($employees) - 1)]);

            $manager->persist($transaction);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ClientFixtures::class, CompanyFixtures::class, ProductFixtures::class, ProviderFixtures::class, EmployeeFixtures::class
        ];
    }
}
