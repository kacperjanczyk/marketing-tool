<?php

namespace App\Entity;

use App\Repository\FacebookAccessRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FacebookAccessRepository::class)]
class FacebookAccess
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $crdate = null;

    #[ORM\Column(length: 255)]
    private ?string $accessToken = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCrdate(): ?\DateTimeInterface
    {
        return $this->crdate;
    }

    public function setCrdate(\DateTimeInterface $crdate): static
    {
        $this->crdate = $crdate;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): static
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}
