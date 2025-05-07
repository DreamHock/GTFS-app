<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type;
use Symfony\Component\Serializer\Annotation\Groups;

#[MongoDB\Document]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(
            denormalizationContext: ['groups' => ['role:write']],
            security: "is_granted('ROLE_ADMIN')"
        )
    ], 
    normalizationContext: ['groups' => ['role:read']]
)]
class Role
{
    #[MongoDB\Id]
    #[Groups(['role:read', 'user:read'])]
    private $id;

    #[MongoDB\Field(type: Type::STRING)]
    #[Groups(['role:read', 'user:read', 'role:write'])]
    private $name;

    #[MongoDB\ReferenceMany(targetDocument: User::class, mappedBy: 'roles', nullable: true)]
    private ?Collection $users;

    #[MongoDB\ReferenceMany(targetDocument: Permission::class, inversedBy: 'roles', nullable: true)]
    #[Groups(['role:read'])]
    private ?Collection $permissions;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addRole($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeRole($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Permission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): self
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }

        return $this;
    }

    public function removePermission(Permission $permission): self
    {
        $this->permissions->removeElement($permission);

        return $this;
    }
}
