<?php

namespace App\Entity;

use App\Repository\SocialMediaPostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SocialMediaPostRepository::class)]
class SocialMediaPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 1000)]
    private ?string $text = null;

    #[ORM\Column(nullable: true)]
    private ?int $publishDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externalPostId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getPublishDate(): ?int
    {
        return $this->publishDate;
    }

    public function setPublishDate(?int $publishDate): static
    {
        $this->publishDate = $publishDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getExternalPostId(): ?string
    {
        return $this->externalPostId;
    }

    public function setExternalPostId(?string $externalPostId): static
    {
        $this->externalPostId = $externalPostId;

        return $this;
    }
}
