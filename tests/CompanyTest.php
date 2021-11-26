<?php

namespace App\Tests\Controller;

use App\Entity\Client;
use App\Entity\Company;
use App\Repository\CompanyRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class CompanyTest extends ApiTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    use RefreshDatabaseTrait;

    public function testGetCompany(): void
    {
        $response = static::createClient()->request('GET', '/api/companies');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Company',
            '@id' => '/api/companies',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 4
        ]);
    }

    public function testPostCompany(): void
    {
        $json = [
            'name' => 'company',
            'balance' => '999.99',
            'country' => 'France'
        ];

        $response = static::createClient()->request('POST', '/api/companies', ['json' => $json]);


        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Company',
            '@id' => "/api/companies/5",
            '@type' => 'Company',
            'name' => 'company',
            'balance' => '999.99',
            'country' => 'France'
        ]);

        $this->assertMatchesResourceItemJsonSchema(Company::class);

        $this->dataCompany(5);
    }


    public function dataCompany($id)
    {
        $client = static::getContainer()->get('doctrine')->getRepository(Company::class)->findOneBy(['id' => $id]);

        $newJson = [
            'name' => $client->getName(),
            'balance' => $client->getBalance(),
            'country' => $client->getCountry()
        ];

        $this->assertJsonContains(json_encode($newJson));
    }


    public function testDeleteCompany(): void
    {
        $client = static::createClient();

        $json = [
            'name' => 'company',
            'balance' => '999.99',
            'country' => 'France'
        ];

        $company = new Company();
        $company->setName('company');
        $company->setBalance(352.25);
        $company->setCountry('France');

        $this->entityManager->persist($company);
        $this->entityManager->flush();

        $client->request('DELETE', '/api/companies/6');

        $this->assertResponseStatusCodeSame(204);

        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Company::class)->findOneBy(['id' => 6])
        );
    }

    public function testUpdateCompany(): void
    {
        $client = static::createClient();
       
        $json = [
            'name' => 'company update',
            'balance' => '0',
            'country' => 'Italy'
        ];

        $response = $client->request('PUT', '/api/companies/4', ['json' => $json]);

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/api/contexts/Company',
            '@id' => "/api/companies/4",
            '@type' => 'Company',
            'name' => 'company update',
            'balance' => '0.00',
            'country' => 'Italy'
        ]);

        $this->assertMatchesResourceItemJsonSchema(Client::class);

    }

}
