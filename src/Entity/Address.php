<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/*#[ORM\Entity(repositoryClass: AddressRepository::class)]*/
#[ORM\Embeddable]
class Address
{
    /*#[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;*/

    #[ORM\Column(type: 'string', length: 255)]
    private $street;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $city;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $region;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $postcode;

    /*#[ORM\OneToOne(mappedBy: 'address',
        targetEntity: Business::class,
        cascade: ['persist', 'remove'])]
    private $business;*/

   /* public function getId(): ?int
    {
        return $this->id;
    }*/

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }


    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }
/*
    public function getBusiness(): ?Business
    {
        return $this->business;
    }*/

 /*   public function setBusiness(?Business $business): self
    {
        // unset the owning side of the relation if necessary
        if ($business === null && $this->business !== null) {
            $this->business->setAddress(null);
        }

        // set the owning side of the relation if necessary
        if ($business !== null && $business->getAddress() !== $this) {
            $business->setAddress($this);
        }

        $this->business = $business;

        return $this;
    }*/

    public function __toString(): string
    {
        return $this->getStreet() .', '. $this->getCity()
            .', '. $this->getRegion() .', '. $this->getPostcode();
    }

}
