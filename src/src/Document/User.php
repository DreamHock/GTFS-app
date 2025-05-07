<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[MongoDB\Document]
#[ApiResource(
    operations: [new GetCollection()],
    denormalizationContext: ['groups' => ['user:write']],
    normalizationContext: ['groups' => ['user:read']],
    security: "is_granted('ROLE_ADMIN')"
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[MongoDB\Id]
    #[Groups(['user:read'])]
    private $id;

    #[MongoDB\Field(type: Type::STRING)]
    #[Groups(['user:write', 'user:read'])]
    private $email;

    #[MongoDB\Field(type: Type::INT)]
    private $verificationCode;

    #[MongoDB\Field(type: Type::DATE_IMMUTABLE)]
    private $verificationCodeExpiredAt;

    /**
     * @var Collection<int, Role> The user roles
     */
    #[MongoDB\ReferenceMany(targetDocument: Role::class, inversedBy: 'users', nullable: true)]
    // #[Groups(['user:read'])]
    private ?Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
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
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles($string = false): array
    {
        $roleNames = $this->roles->map(function (Role $role) {
            return $role->getName();
        })->toArray();

        // guarantee every user at least has ROLE_USER
        if (!in_array('ROLE_USER', $roleNames)) {
            $roleNames[] = 'ROLE_USER';
        }

        return array_unique($roleNames);
    }

    /**
     * @return Collection<int, Role>
     */
    #[Groups(['user:read'])]
    public function getRoleObjects(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);

        return $this;
    }

    public function getVerificationCode(): ?int
    {
        return $this->verificationCode;
    }

    public function setVerificationCode(?int $verificationCode): static
    {
        $this->verificationCode = $verificationCode;
        return $this;
    }

    public function getVerificationCodeExpiredAt(): ?\DateTimeImmutable
    {
        return $this->verificationCodeExpiredAt;
    }

    public function setVerificationCodeExpiredAt(?\DateTimeImmutable $verificationCodeExpiredAt): static
    {
        $this->verificationCodeExpiredAt = $verificationCodeExpiredAt;
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

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        // This method is required by PasswordAuthenticatedUserInterface
        // Since we're not using passwords, we can return an empty string
        return '';
    }
}
