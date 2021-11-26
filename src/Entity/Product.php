<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
#[ApiResource(
    normalizationContext: ['groups' => 'read:Product'],
    denormalizationContext: ['groups' => 'write:Product']
)]
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups('read:Product')]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:Product', 'write:Product'])]
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=12, scale=2)
     */
    #[Groups(['read:Product', 'write:Product'])]
    private $price;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    #[Groups(['read:Product', 'write:Product'])]
    private $tax;

    /**
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:Product', 'write:Product'])]
    private $stock;

    /**
     * @ORM\ManyToOne(targetEntity=Provider::class, inversedBy="products")
     */
    #[Groups(['read:Product', 'write:Product'])]
    private $provider;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="products")
     */
    #[Groups(['read:Product', 'write:Product'])]
    private $company;

    /**
     * @ORM\OneToOne(targetEntity=Transaction::class, inversedBy="product", cascade={"persist", "remove"})
     */
    #[Groups('read:Product')]
    private $transaction;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTax(): ?string
    {
        return $this->tax;
    }

    public function setTax(string $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function setTransaction(Transaction $transaction): self
    {
        $this->transaction = $transaction;

        return $this;
    }
}
