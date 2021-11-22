<?php

namespace App\Entity;

use App\Entity\Product;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\TransactionController;
use App\Repository\TransactionRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
#[ApiResource(
    denormalizationContext: ['groups' => 'write:Transaction'],
    normalizationContext: ['groups' => 'read:Transaction'],
    collectionOperations: [
        'transaction_provider' => [
            'path' => '/transactions/providers',
            'method' => 'post',
            'controller' => TransactionController::class,
            'read' => false,
            'security' => 'is_granted("PUBLIC_ACCESS")',
            'denormalization_context' => ['groups' => 'write:Transaction:Provider']
        ],
        'transaction_client' => [
            'path' => '/transactions/clients',
            'method' => 'post',
            'controller' => TransactionController::class,
            'read' => false,
            'security' => 'is_granted("PUBLIC_ACCESS")',
            'denormalization_context' => ['groups' => 'write:Transaction:Client']
        ]
    ],
)]
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     */
    #[Groups(['read:Transaction'])]
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="transactions")
     */
    #[Groups(['write:Transaction:Client'])]
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Provider::class, inversedBy="transactions")
     */
    private $provider;

    /**
     * @ORM\OneToOne(targetEntity=Product::class, mappedBy="transaction", cascade={"persist", "remove"})
     */
    #[Groups(['write:Transaction:Provider', 'write:Transaction:Client'])]
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="transactions")
     */
    #[Groups('write:Transaction:Provider')]
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity=Employee::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups(['write:Transaction:Provider', 'write:Transaction:Client'])]
    private $employee;

    /**
     * @ORM\Column(type="integer")
     */
    #[Groups(['write:Transaction:Provider', 'write:Transaction:Client'])]
    private $quantity;

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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
