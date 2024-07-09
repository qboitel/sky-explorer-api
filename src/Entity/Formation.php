<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
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
    private ArrayCollection $courses;

    #[ORM\OneToMany(targetEntity: Module::class, mappedBy: 'formation')]
    private ArrayCollection $modules;

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

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCourses(): ArrayCollection
    {
        return $this->courses;
    }

    public function setCourses(ArrayCollection $courses): static
    {
        $this->courses = $courses;

        return $this;
    }

    public function getModules(): string
    {
        return $this->modules;
    }

    public function setModules(string $modules): static
    {
        $this->modules = $modules;

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