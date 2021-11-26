<?php

namespace App\Tests\Controller;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class EmployeeTest extends ApiTestCase
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

    public function testGetEmployee(): void
    {
        $response = static::createClient()->request('GET', '/api/employees');

        $this->assertResponseIsSuccessful();

        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Employee',
            '@id' => '/api/employees',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 20
        ]);
    }

    public function testPostEmployee(): void
    {
        $json = [
            "name" => "employee",
            "birthday" => "1991-11-26T13:57:47.821Z",
            "country" => "France",
            "firstDay" => "2021-11-26T13:57:47.821Z",
            "company" => "/api/companies/4"
        ];

        $response = static::createClient()->request('POST', '/api/employees', ['json' => $json]);


        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Employee',
            '@id' => "/api/employees/21",
            '@type' => 'Employee',
            "name" => "employee",
            "birthday" => "1991-11-26T00:00:00+00:00",
            "country" => "France",
            "firstDay" => "2021-11-26T00:00:00+00:00",
            "company" => "/api/companies/4"
        ]);

        $this->assertMatchesResourceItemJsonSchema(Employee::class);

        $this->dataEmployee(21);
    }


    public function dataEmployee($id)
    {
        $employee = static::getContainer()->get('doctrine')->getRepository(Employee::class)->findOneBy(['id' => $id]);

        $newJson = [
            "name" => $employee->getName(),
            "birthday" => "1991-11-26T00:00:00+00:00",
            "country" => $employee->getCountry(),
            "firstDay" => "2021-11-26T00:00:00+00:00",
            "company" => "/api/companies/4"
        ];

        $this->assertJsonContains(json_encode($newJson));
    }


    public function testDeleteEmployee(): void
    {
        $client = static::createClient();

        $client->request('DELETE', '/api/employees/1');

        $this->assertResponseStatusCodeSame(204);

        $this->assertNull(
            static::getContainer()->get('doctrine')->getRepository(Employee::class)->findOneBy(['id' => 1])
        );
    }

    public function testUpdateEmployee(): void
    {
        $client = static::createClient();
       
        $json = [
            "name" => "employee update",
            "birthday" => "2001-11-26T13:57:47.821Z",
            "country" => "Italy",
            "firstDay" => "2020-11-26T13:57:47.821Z",
            "company" => "/api/companies/1"
        ];

        $response = $client->request('PUT', '/api/employees/4', ['json' => $json]);

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            '@context' => '/api/contexts/Employee',
            '@id' => "/api/employees/4",
            '@type' => 'Employee',
            "name" => "employee update",
            "birthday" => "2001-11-26T00:00:00+00:00",
            "country" => "Italy",
            "firstDay" => "2020-11-26T00:00:00+00:00",
            "company" => "/api/companies/1"
        ]);

        $this->assertMatchesResourceItemJsonSchema(Employee::class);

    }

}
