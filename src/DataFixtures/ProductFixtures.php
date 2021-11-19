<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Product;
use App\DataFixtures\CompanyFixtures;
use App\Repository\CompanyRepository;
use App\DataFixtures\ProviderFixtures;
use App\Repository\ProviderRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private CompanyRepository $companyRepository, private ProviderRepository $providerRepository){}
    
    public function load(ObjectManager $manager): void
    {
        // Permet de générer des données aléatoire
        $faker = Faker\Factory::create('fr_FR');

        for($i = 0; $i < 20; $i++) {
            $product = new Product();
            
            $product->setName($faker->word);
            $product->setPrice($faker->randomFloat(2, 0, 300));
            $product->setTax($faker->randomFloat(2, 0, 10));
            $product->setStock($faker->numberBetween(0, 100));

            // Les 10 premiers articles concerneront une compagnie, les autres un fournisseur
            if($i < 10) {
                $companys = $this->companyRepository->findAll();
                $product->setCompany($companys[$faker->numberBetween(0, count($companys) - 1)]);
            } else {
                $provider = $this->providerRepository->findAll();
                $product->setProvider($provider[$faker->numberBetween(0, count($provider) - 1)]);
            }

            $manager->persist($product);

        }

        $manager->flush();
    }

    
    public function getDependencies() {
        return [
            CompanyFixtures::class, ProviderFixtures::class
        ];
    
    }
}
