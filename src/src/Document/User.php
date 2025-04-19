<?php

namespace App\Security;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type;
use Symfony\Component\Security\Core\User\UserInterface;

#[MongoDB\Document]
#[ApiResource()]
class User implements UserInterface
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: Type::STRING)]
    private $email;

    #[MongoDB\Field(type: Type::INT)]
    private $verificationCode;
    
    #[MongoDB\Field(type: Type::DATE_IMMUTABLE)]
    private $verificationCodeExpiredAt;

    /**
     * @var list<string> The user roles
     */
    #[MongoDB\Field(type: 'collection')]
    private $roles = [];

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
    
}
