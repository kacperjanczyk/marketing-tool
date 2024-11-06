<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ceidgId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $postCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ownerName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ownerSurname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $www = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ceidgUrl = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCeidgId(): ?string
    {
        return $this->ceidgId;
    }

    public function setCeidgId(?string $ceidgId): static
    {
        $this->ceidgId = $ceidgId;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(?string $postCode): static
    {
        $this->postCode = $postCode;

        return $this;
    }

    public function getOwnerName(): ?string
    {
        return $this->ownerName;
    }

    public function setOwnerName(?string $ownerName): static
    {
        $this->ownerName = $ownerName;

        return $this;
    }

    public function getOwnerSurname(): ?string
    {
        return $this->ownerSurname;
    }

    public function setOwnerSurname(?string $ownerSurname): static
    {
        $this->ownerSurname = $ownerSurname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
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

    public function getWww(): ?string
    {
        return $this->www;
    }

    public function setWww(?string $www): static
    {
        $this->www = $www;

        return $this;
    }

    public function getCeidgUrl(): ?string
    {
        return $this->ceidgUrl;
    }

    public function setCeidgUrl(?string $ceidgUrl): static
    {
        $this->ceidgUrl = $ceidgUrl;

        return $this;
    }
}
