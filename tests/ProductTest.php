<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class ProductTest extends ApiTestCase
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

    public function testGetProduct(): void
    {
        $response = static::createClient()->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => '/api/products',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 20
        ]);
    }

    public function testPostProduct(): void
    {
        $json = [
            "name" => "Product",
            "price" => "25.0",
            "tax" => "10.5",
            "stock" => 5,
            "provider" => null,
            "company" => "/api/companies/1"

        ];

        $response = static::createClient()->request('POST', '/api/products', ['json' => $json]);


        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "name" => "Product",
            "price" => "25.00",
            "tax" => "10.50",
            "stock" => 5,
            "company" => "/api/companies/1"
        ]);

        $this->assertMatchesResourceItemJsonSchema(Product::class);

        $this->dataProduct(21);
    }


    public function dataProduct($id)
    {
        $product = static::getContainer()->get('doctrine')->getRepository(Product::class)->findOneBy(['id' => $id]);

        $newJson = [
            "name" => $product->getName(),
            "price" => $product->getPrice(),
            "tax" => $product->getTax(),
            "stock" => $product->getStock(),
            "company" => "/api/companies/1"
        ];

        $this->assertJsonContains(json_encode($newJson));
    }


    public function testDeleteProduct(): void
    {
        
        $client = static::createClient();

       
        $client->request('DELETE', '/api/products/8');

        $this->assertResponseStatusCodeSame(204);

        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Product::class)->findOneBy(['id' => 8])
        );
    }

    public function testUpdateProduct(): void
    {
        $client = static::createClient();
       
        $json = [
            "name" => "Product",
            "price" => "25.00",
            "tax" => "10.50",
            "stock" => 5,
            "company" => "/api/companies/1"
        ];

        $response = $client->request('PUT', '/api/products/4', ['json' => $json]);

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/api/contexts/Product',
            '@id' => "/api/products/4",
            '@type' => 'Product',
            "name" => "Product",
            "price" => "25.00",
            "tax" => "10.50",
            "stock" => 5,
            "company" => "/api/companies/1"
        ]);

        $this->assertMatchesResourceItemJsonSchema(Product::class);

    }
}
