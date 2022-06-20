<?php

    namespace App\Entity;

    use App\Repository\ClientRepository;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation\Blameable;

    #[ORM\Entity(repositoryClass: ClientRepository::class)]
    class Client extends Business
    {
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'integer')]
        private $id;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'clients')]
        #[ORM\JoinColumn(nullable: false)]
        #[Blameable(on: 'create')]
        private $user;

        #[ORM\Column(type: 'string', length: 180, nullable: true)]
        private $email;

        #[ORM\OneToMany(mappedBy: 'client', targetEntity: DeliveryNote::class)]
        private $deliveryNotes;

        #[ORM\Column(type: 'string', length: 15, nullable: true)]
        private $cif;

        public function __construct()
        {
            $this->deliveryNotes = new ArrayCollection();
        }

        public function __toString(): string
        {
            return $this->getName();
        }

        public function getId(): ?int
        {
            return parent::getId();
        }

        public function getUser(): ?User
        {
            return $this->user;
        }

        public function setUser(?User $user): self
        {
            $this->user = $user;

            return $this;
        }

        public function getEmail(): ?string
        {
            return $this->email;
        }

        public function setEmail(?string $email): self
        {
            $this->email = $email;

            return $this;
        }

        /**
         * @return Collection<int, DeliveryNote>
         */
        public function getDeliveryNotes(): Collection
        {
            return $this->deliveryNotes;
        }

        public function addDeliveryNote(DeliveryNote $deliveryNote): self
        {
            if (!$this->deliveryNotes->contains($deliveryNote)) {
                $this->deliveryNotes[] = $deliveryNote;
                $deliveryNote->setClient($this);
            }

            return $this;
        }

        public function removeDeliveryNote(DeliveryNote $deliveryNote): self
        {
            if ($this->deliveryNotes->removeElement($deliveryNote) && $deliveryNote->getClient() === $this) {
                    $deliveryNote->setClient(null);
            }

            return $this;
        }

        public function getCif(): ?string
        {
            return $this->cif;
        }

        public function setCif(?string $cif): self
        {
            $this->cif = $cif;

            return $this;
        }
    }
