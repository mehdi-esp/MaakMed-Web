<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'patient' => Patient::class,
    'doctor' => Doctor::class,
    'pharmacy' => Pharmacy::class,
    'admin' => Admin::class,
])]
#[ApiResource]
#[UniqueEntity(
    fields: ['username'],
    message: 'There is already an account with this username',
    entityClass: self::class,
)]
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    protected ?string $username = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 255)]
    protected ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $email = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Issue::class, orphanRemoval: true)]
    private Collection $issues;

    public function __construct()
    {
        $this->issues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->username;
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


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Issue>
     */
    public function getIssues(): Collection
    {
        return $this->issues;
    }

    public function addIssue(Issue $issue): static
    {
        if (!$this->issues->contains($issue)) {
            $this->issues->add($issue);
            $issue->setUser($this);
        }

        return $this;
    }

    public function removeIssue(Issue $issue): static
    {
        if ($this->issues->removeElement($issue)) {
            // set the owning side to null (unless already changed)
            if ($issue->getUser() === $this) {
                $issue->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {

        $instanceRole = match (true) {
            $this instanceof Patient => 'ROLE_PATIENT',
            $this instanceof Doctor => 'ROLE_DOCTOR',
            $this instanceof Pharmacy => 'ROLE_PHARMACY',
            $this instanceof Admin => 'ROLE_ADMIN'
        };
        return [
            'ROLE_USER',
            $instanceRole,
        ];
    }

    public function getRoleName(): string
    {

        return match (true) {
            $this instanceof Patient => 'Patient',
            $this instanceof Doctor => 'Doctor',
            $this instanceof Pharmacy => 'Pharmacy',
            $this instanceof Admin => 'Admin'
        };
    }

    public function getIconPath(): string
    {
        return match (true) {
            $this instanceof Patient => 'patient.png',
            $this instanceof Doctor => 'doctor.png',
            $this instanceof Pharmacy => 'pharmacy.png',
            $this instanceof Admin => 'admin.png'
        };
    }

    public function getAvatarString(): string
    {
        return match (true) {
            $this instanceof Patient, $this instanceof Doctor => ($this->getFirstName()[0] ?? "") . ($this->getLastName()[0] ?? ""),
            $this instanceof Pharmacy => substr($this->getName() ?? "", 0, 2),
            $this instanceof Admin => substr($this->getUsername(), 0, 2)
        };
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }
}
