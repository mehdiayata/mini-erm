<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class ClientTest extends ApiTestCase
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

    public function testGetClient(): void
    {
        $response = static::createClient()->request('GET', '/api/clients');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Client',
            '@id' => '/api/clients',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 6
        ]);
    }

    public function testPostClient(): void
    {
        $json = [
            'name' => 'client',
            'adress' => 'Rue du test',
            'country' => 'France'
        ];

        $response = static::createClient()->request('POST', '/api/clients', ['json' => $json]);


        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Client',
            '@id' => "/api/clients/7",
            '@type' => 'Client',
            'name' => 'client',
            'adress' => 'Rue du test',
            'country' => 'France'
        ]);

        $this->assertMatchesResourceItemJsonSchema(Client::class);

        $this->dataClient(7);
    }

   
    public function dataClient($id)
    {
        $client = static::getContainer()->get('doctrine')->getRepository(Client::class)->findOneBy(['id' => $id]);
               
        $newJson = [
                'name' => $client->getName(),
                'adress' => $client->getAdress(),
                'country' => $client->getCountry()     
            ];

        $this->assertJsonContains(json_encode($newJson));
        
    }


    public function testDeleteClient(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(Client::class, ['id' => 5]);

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);

        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Client::class)->findOneBy(['id' => 5])
        );
    }

    public function testUpdateClient(): void
    {
        $client = static::createClient();
        $json = [
            'name' => 'client update',
            'adress' => 'adress update',
            'country' => 'country update'
        ];


        $response = $client->request('PUT', '/api/clients/5', ['json' => $json]);
        
        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/api/contexts/Client',
            '@id' => "/api/clients/5",
            '@type' => 'Client',
            'name' => 'client update',
            'adress' => 'adress update',
            'country' => 'country update'
        ]);

        $this->assertMatchesResourceItemJsonSchema(Client::class);

    }

}
