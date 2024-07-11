<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'formations')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource]
class Formation implements \Stringable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(type: Types::FLOAT)]
    private float $price;

    #[ORM\ManyToMany(targetEntity: Course::class)]
    private Collection $courses;

    #[ORM\OneToMany(targetEntity: Module::class, mappedBy: 'formation')]
    private Collection $modules;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->courses = new ArrayCollection();
        $this->modules = new ArrayCollection();
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

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): static
    {
        $this->modules->add($module);

        return $this;
    }

    public function removeModule(Module $module): static
    {
        $this->modules->removeElement($module);

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function addCourse(Course $course): static
    {
        $this->courses->add($course);

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        $this->courses->removeElement($course);

        return $this;
    }
}