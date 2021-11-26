<?php

namespace App\Tests\Controller;

use App\Entity\Provider;
use App\Repository\ProviderRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class ProviderTest extends ApiTestCase
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

    public function testGetProvider(): void
    {
        $response = static::createClient()->request('GET', '/api/providers');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Provider',
            '@id' => '/api/providers',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 6
        ]);
    }

    public function testPostProvider(): void
    {
        $json = [
            "name" => "Provider",
            "adress" => "Provider adress",
            "country" => "France"
        ];

        $response = static::createClient()->request('POST', '/api/providers', ['json' => $json]);


        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Provider',
            '@id' => "/api/providers/7",
            '@type' => 'Provider',
            "name" => "Provider",
            "adress" => "Provider adress",
            "country" => "France"
        ]);

        $this->assertMatchesResourceItemJsonSchema(Provider::class);

        $this->dataProvider(7);
    }


    public function dataProvider($id)
    {
        $provider = static::getContainer()->get('doctrine')->getRepository(Provider::class)->findOneBy(['id' => $id]);

        $newJson = [
            "name" => $provider->getName(),
            "adress" => $provider->getAdress(),
            "country" => $provider->getCountry()
        ];

        $this->assertJsonContains(json_encode($newJson));
    }


    public function testDeleteProvider(): void
    {
        
        $client = static::createClient();

        // Create a new provider for deleting
    
        $provider = new Provider();
        $provider->setName('name provider');
        $provider->setAdress('adress provider');
        $provider->setCountry('Italy');

        $this->entityManager->persist($provider);
        $this->entityManager->flush();

        $client->request('DELETE', '/api/providers/8');

        $this->assertResponseStatusCodeSame(204);

        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Provider::class)->findOneBy(['id' => 8])
        );
    }

    public function testUpdateProvider(): void
    {
        $client = static::createClient();
       
        $json = [
            "name" => "Provider",
            "adress" => "Provider adress",
            "country" => "France"
        ];

        $response = $client->request('PUT', '/api/providers/4', ['json' => $json]);

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/api/contexts/Provider',
            '@id' => "/api/providers/4",
            '@type' => 'Provider',
            "name" => "Provider",
            "adress" => "Provider adress",
            "country" => "France"
        ]);

        $this->assertMatchesResourceItemJsonSchema(Provider::class);

    }
}
