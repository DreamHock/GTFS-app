<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\Types\Type;
use Symfony\Component\Serializer\Annotation\Groups;

#[MongoDB\Document]
#[ApiResource(operations: [new GetCollection()], normalizationContext: ['groups' => ['rule:read']])]
class PermissionRule
{
    #[MongoDB\Id]
    #[Groups(['rule:read', 'permission:read'])]
    private $id;

    #[MongoDB\ReferenceOne(targetDocument: Permission::class, inversedBy: 'rules')]
    #[Groups(['rule:read'])]
    private ?Permission $permission = null;

    #[MongoDB\Field(type: Type::STRING)]
    #[Groups(['rule:read', 'permission:read'])]
    private ?string $description = null;

    #[MongoDB\Field(type: Type::HASH)]
    #[Groups(['rule:read', 'permission:read'])]
    private array $conditions = [];

    #[MongoDB\Field(type: Type::STRING, nullable: true)]
    #[Groups(['rule:read', 'permission:read'])]
    private ?string $subjectType = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPermission(): ?Permission
    {
        return $this->permission;
    }

    public function setPermission(?Permission $permission): self
    {
        $this->permission = $permission;
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

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function setConditions(array $conditions): self
    {
        $this->conditions = $conditions;
        return $this;
    }

    public function getSubjectType(): ?string
    {
        return $this->subjectType;
    }

    public function setSubjectType(?string $subjectType): self
    {
        $this->subjectType = $subjectType;
        return $this;
    }
}