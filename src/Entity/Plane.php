<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Traits\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'planes')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => ['plane:read']],
    denormalizationContext: ['groups' => ['plane:write']]
)]
class Plane
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['plane:read'])]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['plane:read', 'plane:write'])]
    private string $immatriculation;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['plane:read', 'plane:write'])]
    private string $type;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['plane:read', 'plane:write', 'course:read'])]
    private string $model;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['plane:read', 'plane:write', 'course:read'])]
    private string $image;

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

    public function getImmatriculation(): string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): static
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function __toString(): string
    {
        return $this->immatriculation;
    }
}