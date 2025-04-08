<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * GTFS Feed Document
 */
#[MongoDB\Document]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['gtfs:read']],
    denormalizationContext: ['groups' => ['gtfs:write']]
)]
class GtfsFeed
{
    #[MongoDB\Id(strategy: "AUTO")]
    #[Groups(['gtfs:read'])]
    private $id;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank]
    #[Groups(['gtfs:read', 'gtfs:write'])]
    private $title;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank]
    #[Assert\Url]
    #[Groups(['gtfs:read', 'gtfs:write'])]
    private $feedUrl;

    #[MongoDB\Field(type: "string")]
    #[Groups(['gtfs:read', 'gtfs:write'])]
    private $version;

    #[MongoDB\Field(type: "date")]
    #[Groups(['gtfs:read'])]
    private $lastUpdated;

    #[MongoDB\Field(type: "hash")]
    #[Groups(['gtfs:read', 'gtfs:write'])]
    private $metadata = [];

    #[MongoDB\Field(type: "boolean")]
    #[Groups(['gtfs:read', 'gtfs:write'])]
    private $active = true;

    public function __construct()
    {
        $this->lastUpdated = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getFeedUrl(): ?string
    {
        return $this->feedUrl;
    }

    public function setFeedUrl(string $feedUrl): self
    {
        $this->feedUrl = $feedUrl;
        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function getLastUpdated(): ?\DateTime
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(\DateTime $lastUpdated): self
    {
        $this->lastUpdated = $lastUpdated;
        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }
}
