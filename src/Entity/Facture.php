<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'factures')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource]
class Facture
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\Column(type: Types::FLOAT)]
    private float $total;

    #[ORM\OneToMany(targetEntity: FactureItem::class, mappedBy: 'facture')]
    private ArrayCollection $items;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getItems(): ArrayCollection
    {
        return $this->items;
    }

    public function addItem(FactureItem $item): static
    {
        $this->items->add($item);

        return $this;
    }

    public function removeItem(FactureItem $item): static
    {
        $this->items->removeElement($item);

        return $this;
    }
}