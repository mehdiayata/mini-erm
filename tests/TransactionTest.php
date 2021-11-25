<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class TransactionTest extends ApiTestCase
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

    // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    public function testGetTransaction(): void
    {
        $response = static::createClient()->request('GET', '/api/transactions');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Transaction',
            '@id' => '/api/transactions',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 10
        ]);
    }

    public function testPostTransactionProvider(): void
    {
        $json = [
            'product' => '/api/products/6',
            'company' => '/api/companies/1',
            'employee' => '/api/employees/1',
            'quantity' => 4
        ];

        $response = static::createClient()->request('POST', '/api/transactions/providers', ['json' => $json]);


        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Transaction',
            '@id' => "/api/transactions/11",
            '@type' => 'Transaction',
            'product' => '/api/products/6',
            'company' => '/api/companies/1',
            'employee' => '/api/employees/1',
            'quantity' => 4
        ]);

        $this->assertMatchesResourceItemJsonSchema(Transaction::class);

        $this->dataTransaction(6, $response->getInfo('url'));
    }

    public function testPostTransactionClient(): void
    {
        $response = static::createClient()->request('POST', '/api/transactions/clients', ['json' => [
            'product' => '/api/products/8',
            'client' => '/api/clients/1',
            'employee' => '/api/employees/1',
            'quantity' => 5
        ]]);

        $this->assertResponseStatusCodeSame(201);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Transaction',
            '@type' => 'Transaction',
            'product' => '/api/products/8',
            'client' => '/api/clients/1',
            'employee' => '/api/employees/1',
            'quantity' => 5
        ]);

        $this->assertMatchesResourceItemJsonSchema(Transaction::class);

        $this->dataTransaction(8, $response->getInfo('url'));
    }


    public function dataTransaction($id, $url)
    {
        $product = static::getContainer()->get('doctrine')->getRepository(Product::class)->findOneBy(['id' => $id]);

        $transaction = $product->getTransaction();

        if (strpos($url, 'providers')) {

            $newJson = [
                'product' => '/api/products/' . $product->getId(),
                'company' => '/api/companies/' . $product->getCompany()->getId(),
                'employee' => '/api/employees/' . $transaction->getEmployee()->getId(),
                'quantity' => $transaction->getQuantity()
            ];
        } else if (strpos($url, 'clients')) {
            $newJson = [
                'product' => '/api/products/' . $product->getId(),
                'client' => '/api/clients/' . $transaction->getClient()->getId(),
                'employee' => '/api/employees/' . $transaction->getEmployee()->getId(),
                'quantity' => $transaction->getQuantity()
            ];
        }

        $this->assertJsonContains(json_encode($newJson));
    }

    public function testInvalidStock(): void
    {
        $response = static::createClient()->request('POST', '/api/transactions/clients', ['json' => [
            'product' => '/api/products/8',
            'client' => '/api/clients/1',
            'employee' => '/api/employees/1',
            'quantity' => 27
        ]]);

        $this->assertResponseStatusCodeSame(500);

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');


        $this->assertJsonContains([
            '@context' => "/api/contexts/Error",
            '@type' => "hydra:Error",
            "hydra:title" => "An error occurred",
            "hydra:description" => "This quantity of product is inferior as stock",
        ]);

        $this->assertMatchesResourceItemJsonSchema(Transaction::class);

    }

    public function testIfProductIsSold():void
     {
        {
            $response = static::createClient()->request('POST', '/api/transactions/clients', ['json' => [
                'product' => '/api/products/1',
                'client' => '/api/clients/1',
                'employee' => '/api/employees/1',
                'quantity' => 27
            ]]);
    
            $this->assertResponseStatusCodeSame(500);
    
            $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    
    
            $this->assertJsonContains([
                '@context' => "/api/contexts/Error",
                '@type' => "hydra:Error",
                "hydra:title" => "An error occurred",
                "hydra:description" => "The product is already sold"
            ]);
    
            $this->assertMatchesResourceItemJsonSchema(Transaction::class);
    
        }
    }
}
