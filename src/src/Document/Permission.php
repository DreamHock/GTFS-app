<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type;
use Symfony\Component\Serializer\Annotation\Groups;

#[MongoDB\Document]
#[ApiResource(operations: [new GetCollection()], normalizationContext: ['groups' => ['permission:read']])]
class Permission
{
    #[MongoDB\Id]
    #[Groups(['permission:read', 'role:read'])]
    private $id;

    #[MongoDB\Field(type: Type::STRING)]
    #[Groups(['permission:read', 'role:read'])]
    private $name;

    #[MongoDB\Field(type: Type::STRING)]
    #[Groups(['permission:read', 'role:read'])]
    private $description;

    #[MongoDB\ReferenceMany(targetDocument: Role::class, mappedBy: 'permissions')]
    private Collection $roles;

    #[MongoDB\ReferenceMany(targetDocument: PermissionRule::class, mappedBy: 'permission')]
    #[Groups(['permission:read'])]
    private Collection $rules;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->rules = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->addPermission($this);
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        if ($this->roles->removeElement($role)) {
            $role->removePermission($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, PermissionRule>
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function addRule(PermissionRule $rule): self
    {
        if (!$this->rules->contains($rule)) {
            $this->rules->add($rule);
            $rule->setPermission($this);
        }

        return $this;
    }

    public function removeRule(PermissionRule $rule): self
    {
        if ($this->rules->removeElement($rule)) {
            if ($rule->getPermission() === $this) {
                $rule->setPermission(null);
            }
        }

        return $this;
    }
}