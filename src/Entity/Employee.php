<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EmployeeRepository::class)
 */
#[ApiResource(
    normalizationContext: ['groups' => 'read:Employee'],
    denormalizationContext: ['groups' => 'write:Employee']
)]
class Employee
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups('read:Employee')]
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:Employee', 'write:Employee'])]
    private $name;

    /**
     * @ORM\Column(type="date")
     */
    #[Groups(['read:Employee', 'write:Employee'])]
    private $birthday;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:Employee', 'write:Employee'])]
    private $country;

    /**
     * @ORM\Column(type="date")
     */
    #[Groups(['read:Employee', 'write:Employee'])]
    private $firstDay;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="employees")
     */
    #[Groups(['read:Employee', 'write:Employee'])]
    private $company;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="employee")
     */
    #[Groups('read:Employee')]
    private $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

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

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getFirstDay(): ?\DateTimeInterface
    {
        return $this->firstDay;
    }

    public function setFirstDay(\DateTimeInterface $firstDay): self
    {
        $this->firstDay = $firstDay;

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

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setEmployee($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getEmployee() === $this) {
                $transaction->setEmployee(null);
            }
        }

        return $this;
    }
}
