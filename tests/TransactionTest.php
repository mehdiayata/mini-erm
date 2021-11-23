<?php

// namespace App\Tests\Controller;

// use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
// use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

// class TransactionTest extends ApiTestCase
// {  

//     // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
//     use RefreshDatabaseTrait;
  
//     public function testGetTransaction(): void
//     {
//         $response = static::createClient()->request('GET', '/api/transactions');

//         $this->assertResponseIsSuccessful();

//         $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

//         $this->assertJsonContains([
//             '@context' => '/api/contexts/Transaction',
//             '@id' => '/api/transactions',
//             '@type' => 'hydra:Collection',
//             'hydra:totalItems' => 0
//         ]);
//     }
// }
