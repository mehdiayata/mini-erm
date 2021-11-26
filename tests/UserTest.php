<?php

namespace App\Tests;

use App\Entity\User;
use App\DataFixtures\UserFixtures;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class UserTest extends ApiTestCase
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
  
    public function testGetUser(): void
    {
        $response = static::createClient()->request('GET', '/api/users');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 0
        ]);
    }

    public function testRegistration() {

        $user = new User();
        $json = [
            'email' => "test2@test.fr",
            'password' =>  self::getContainer()->get('security.user_password_hasher')->hashPassword($user, 'mini-erp')
        ];

        $response = static::createClient()->request('POST', '/api/registration', ['json' => $json]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testToken() {
        
        $client = static::createClient();
        $user = new User();
        $user->setEmail('test@test.fr');
        $user->setPassword(self::getContainer()->get('security.user_password_hasher')->hashPassword($user, 'minierp'));
      
        $this->entityManager->persist($user);
        $this->entityManager->flush();


        $response = $client->request('POST', '/api/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'test@test.fr',
                'password' => 'minierp'
            ],
        ]);


        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);
    }
}
