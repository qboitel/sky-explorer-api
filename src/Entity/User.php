<?php

namespace App\Entity;

use DateTimeImmutable;
use Stringable;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use App\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity('email', 'username')]
#[ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, Stringable
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['user:read', 'reservation:read'])]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Groups(['user:read', 'user:write', 'reservation:read'])]
    private string $lastName;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Groups(['user:read', 'user:write', 'reservation:read'])]
    private string $firstName;

    #[ORM\Column(type: Types::STRING, length: 10)]
    #[Groups(['user:read', 'user:write', 'reservation:read'])]
    private string $phone;

    #[ORM\Column(type: Types::STRING, length: 180)]
    #[Groups(['user:read', 'user:write', 'reservation:read'])]
    private string $email;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user:read', 'user:write', 'reservation:read'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups('user:write')]
    private string $password;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups(['user:read', 'user:write', 'reservation:read'])]
    private bool $active = true;

    #[ORM\OneToMany(targetEntity: License::class, mappedBy: 'user', cascade: ['remove'])]
    #[Groups(['user:read', 'user:write', 'reservation:read'])]
    private Collection $licenses;

    #[ORM\OneToMany(targetEntity: MedicalCertificate::class, mappedBy: 'user', cascade: ['remove'])]
    #[Groups(['user:read', 'user:write', 'reservation:read'])]
    private Collection $medicalCertificates;

    #[ORM\Column(type: Types::STRING, length: 36, unique: true, nullable: false)]
    #[Groups(['user:read', 'user:write', 'reservation:read'])]
    private string $username;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['user:read', 'user:write', 'reservation:read'])]
    private ?DateTimeImmutable $lastConnectedAt = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->licenses = new ArrayCollection();
        $this->medicalCertificates = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->id;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): void
    {
        $this->roles[] = $role;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getLicenses(): Collection
    {
        return $this->licenses;
    }

    public function addLicense(License $license): self
    {
        if (!$this->licenses->contains($license)) {
            $this->licenses[] = $license;
            $license->setUser($this);
        }

        return $this;
    }

    public function removeLicense(License $license): self
    {
        // set the owning side to null (unless already changed)
        if ($this->licenses->removeElement($license) && $license->getUser() === $this) {
            $license->setUser(null);
        }

        return $this;
    }

    public function getMedicalCertificates(): Collection
    {
        return $this->medicalCertificates;
    }

    public function addMedicalCertificate(MedicalCertificate $medicalCertificate): self
    {
        if (!$this->medicalCertificates->contains($medicalCertificate)) {
            $this->medicalCertificates[] = $medicalCertificate;
            $medicalCertificate->setUser($this);
        }

        return $this;
    }

    public function removeMedicalCertificate(MedicalCertificate $medicalCertificate): self
    {
        // set the owning side to null (unless already changed)
        if ($this->medicalCertificates->removeElement($medicalCertificate) && $medicalCertificate->getUser() === $this) {
            $medicalCertificate->setUser(null);
        }

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getLastConnectedAt(): ?DateTimeImmutable
    {
        return $this->lastConnectedAt;
    }

    public function setLastConnectedAt(?DateTimeImmutable $lastConnectedAt): self
    {
        $this->lastConnectedAt = $lastConnectedAt;

        return $this;
    }
}
