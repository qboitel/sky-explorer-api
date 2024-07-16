<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'modules')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => ['module:read']],
    denormalizationContext: ['groups' => ['module:write']]
)]
class Module implements \Stringable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['module:read', 'formation:read', 'reservation:read'])]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Groups(['module:read', 'module:write', 'formation:read', 'reservation:read'])]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['module:read', 'module:write'])]
    private string $description;

    #[ORM\ManyToOne(targetEntity: Formation::class)]
    #[Groups(['module:read', 'module:write'])]
    private Formation $formation;

    #[ORM\ManyToOne(targetEntity: Plane::class)]
    #[Groups(['module:read', 'module:write', 'reservation:read'])]
    private Plane $plane;

    #[ORM\Column(type: Types::FLOAT)]
    #[Groups(['module:read', 'module:write', 'reservation:read'])]
    private float $price;

    #[ORM\OneToMany(targetEntity: Competence::class, mappedBy: 'module')]
    #[Groups(['module:read', 'module:write'])]
    private Collection $competences;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->competences = new ArrayCollection();
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

    public function getFormation(): Formation
    {
        return $this->formation;
    }

    public function setFormation(Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    public function getCompetences(): Collection
    {
        return $this->competences;
    }

    public function addCompetence(Competence $competence): static
    {
        $this->competences->add($competence);

        return $this;
    }

    public function removeCompetence(Competence $competence): static
    {
        $this->competences->removeElement($competence);

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

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}