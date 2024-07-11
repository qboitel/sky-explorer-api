<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Traits\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'courses')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => ['course:read']],
    denormalizationContext: ['groups' => ['course:write']],
)]
class Course implements \Stringable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['course:read', 'formation:read'])]
    private Uuid $id;

    #[ORM\Column(type: 'string')]
    #[Groups(['course:read', 'course:write', 'formation:read'])]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['course:read', 'course:write'])]
    private string $description;

    #[ORM\ManyToOne(targetEntity: Plane::class)]
    #[Groups(['course:read', 'course:write'])]
    private Plane $plane;

    #[ORM\ManyToOne(targetEntity: Formation::class)]
    #[Groups(['course:read', 'course:write'])]
    private Formation $formation;

    #[ORM\Column(type: Types::FLOAT)]
    #[Groups(['course:read', 'course:write'])]
    private float $price;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['course:read', 'course:write'])]
    private string $subjects;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPlane(): Plane
    {
        return $this->plane;
    }

    public function setPlane(Plane $plane): static
    {
        $this->plane = $plane;

        return $this;
    }

    public function getFormation(): Formation
    {
        return $this->formation;
    }

    public function setFormation(Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getSubjects(): string
    {
        return $this->subjects;
    }

    public function setSubjects(string $subjects): static
    {
        $this->subjects = $subjects;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}