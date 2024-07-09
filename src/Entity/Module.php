<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'modules')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource]
class Module implements \Stringable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\ManyToOne(targetEntity: Formation::class)]
    private Formation $formation;

    #[ORM\OneToMany(targetEntity: Competence::class, mappedBy: 'module')]
    private ArrayCollection $competences;

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

    public function getFormation(): Formation
    {
        return $this->formation;
    }

    public function setFormation(Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

    public function getCompetences(): ArrayCollection
    {
        return $this->competences;
    }

    public function setCompetences(ArrayCollection $competences): static
    {
        $this->competences = $competences;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}