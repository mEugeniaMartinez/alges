<?php

    namespace App\Entity;

    use App\Repository\BusinessRepository;
    use Doctrine\ORM\Mapping as ORM;

    #[ORM\Entity(repositoryClass: BusinessRepository::class)]
    #[ORM\InheritanceType("JOINED")]
    #[ORM\DiscriminatorColumn(name: "type", type: "string")]
    #[ORM\DiscriminatorMap([
        "client" => Client::class,
        "user" => User::class
    ])]
    abstract class Business
    {
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'integer')]
        private $id;

        #[ORM\Column(type: 'string', length: 255)]
        private $name;

        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $phone;

        #[ORM\Embedded(class: Address::class)]
        private $address;

        public function getId(): ?int
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

        public function getPhone(): ?string
        {
            return $this->phone;
        }

        public function setPhone(?string $phone): self
        {
            $this->phone = $phone;

            return $this;
        }

        public function getAddress(): ?Address
        {
            return $this->address;
        }

        public function setAddress(?Address $address): self
        {
            $this->address = $address;

            return $this;
        }

        public function __construct()
        {
            $this->address = new Address();
        }

        public function getFullAddress(): string
        {
            return sprintf('%s, %s, %s, %s',
                $this->getAddress()->getStreet(),
                $this->getAddress()->getCity(),
                $this->getAddress()->getRegion(),
                $this->getAddress()->getPostcode(),
            );
        }
    }
