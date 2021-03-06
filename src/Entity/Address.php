<?php

    namespace App\Entity;

    use Doctrine\ORM\Mapping as ORM;

    #[ORM\Embeddable]
    class Address
    {
        #[ORM\Column(type: 'string', length: 255)]
        private $street;

        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $city;

        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $region;

        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $postcode;

        public function __construct()
        {
            $this->street = " ";
        }

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

        public function __toString(): string
        {
            return $this->getStreet() . ', ' . $this->getCity()
                . ', ' . $this->getRegion() . ', ' . $this->getPostcode();
        }

    }
