<?php

namespace App\Tests\Controller;

use App\Entity\Transaction;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class TransactionTest extends ApiTestCase
{

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
        $response = static::createClient()->request('POST', '/api/transactions/providers', ['json' => [
            'product' => '/api/products/1',
            'company' => '/api/companies/1',
            'employee' => '/api/employees/1',
            'quantity' => 4
        ]]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
       
        $this->assertJsonContains([
            '@context' => '/api/contexts/Transaction',
            '@type' => 'Transaction'
        ]);

         $this->assertMatchesResourceItemJsonSchema(Transaction::class);

    }

    public function testPostTransactionClient(): void
    {
        $response = static::createClient()->request('POST', '/api/transactions/clients', ['json' => [
            'product' => '/api/products/1',
            'client' => '/api/clients/1',
            'employee' => '/api/employees/1',
            'quantity' => 5
        ]]);
        $this->assertResponseStatusCodeSame(201);
        
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
       
        $this->assertJsonContains([
            '@context' => '/api/contexts/Transaction',
            '@type' => 'Transaction'
        ]);

         $this->assertMatchesResourceItemJsonSchema(Transaction::class);

    }

}
