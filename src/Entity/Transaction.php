<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
#[ApiResource]
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Provider::class, inversedBy="transactions")
     */
    private $provider;

    /**
     * @ORM\OneToOne(targetEntity=Product::class, mappedBy="transaction", cascade={"persist", "remove"})
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="transactions")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity=Employee::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $employee;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        // set the owning side of the relation if necessary
        if ($product->getTransaction() !== $this) {
            $product->setTransaction($this);
        }

        $this->product = $product;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): self
    {
        $this->employee = $employee;

        return $this;
    }
}
