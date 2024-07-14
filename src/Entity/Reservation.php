<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Enum\Type;
use App\Filter\TypedDateFilter;
use App\Filter\TypedSearchFilter;
use App\Traits\TimestampableTrait;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'reservations')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => ['reservation:read']],
    denormalizationContext: ['groups' => ['reservation:write']]
)]
#[ApiFilter(filterClass: TypedDateFilter::class, properties: [
    'startsAt' => Types::DATETIME_IMMUTABLE,
    'endsAt' => Types::DATETIME_IMMUTABLE,
])]
#[ApiFilter(filterClass: TypedSearchFilter::class, properties: [
    'user' => 'exact',
    'user.id' => 'exact',
])]
class Reservation implements \Stringable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['reservation:read'])]
    private Uuid $id;

    #[ORM\Column(type: Types::INTEGER, enumType: Type::class)]
    #[Groups(['reservation:read', 'reservation:write'])]
    private Type $type;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['reservation:read', 'reservation:write'])]
    private User $user;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['reservation:read', 'reservation:write'])]
    private User $instructor;

    #[ORM\ManyToOne(targetEntity: Module::class)]
    #[Groups(['reservation:read', 'reservation:write'])]
    private Module $module;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['reservation:read', 'reservation:write'])]
    private \DateTimeImmutable $startsAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['reservation:read', 'reservation:write'])]
    private \DateTimeImmutable $endsAt;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['reservation:read', 'reservation:write'])]
    private string $feedbacks;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['reservation:read', 'reservation:write'])]
    private string $followUp;

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

    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): static
    {
        $this->type = $type;

        return $this;
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

    public function getInstructor(): User
    {
        return $this->instructor;
    }

    public function setInstructor(User $instructor): static
    {
        $this->instructor = $instructor;

        return $this;
    }

    public function getModule(): Module
    {
        return $this->module;
    }

    public function setModule(Module $module): static
    {
        $this->module = $module;

        return $this;
    }

    public function getStartsAt(): \DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function setStartsAt(\DateTimeImmutable $startsAt): static
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): \DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(\DateTimeImmutable $endsAt): static
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    public function getFeedbacks(): string
    {
        return $this->feedbacks;
    }

    public function setFeedbacks(string $feedbacks): static
    {
        $this->feedbacks = $feedbacks;

        return $this;
    }

    public function getFollowUp(): string
    {
        return $this->followUp;
    }

    public function setFollowUp(string $followUp): static
    {
        $this->followUp = $followUp;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}