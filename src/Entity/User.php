<?php

    namespace App\Entity;

    use App\Repository\UserRepository;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
    use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Validator\Constraints as Assert;

    #[ORM\Entity(repositoryClass: UserRepository::class)]
    #[UniqueEntity(fields: ['email'], message: 'Ya existe una cuenta con ese email')]
    class User extends Business implements UserInterface, PasswordAuthenticatedUserInterface
    {
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'integer')]
        private $id;

        #[ORM\Column(type: 'string', length: 180, nullable: false)]
        #[Assert\Email]
        private $email;

        #[ORM\Column(nullable: true)]
        private ?string $logo;

        #[ORM\Column(type: 'string', length: 1500, nullable: true)]
        private $footer;

        #[ORM\Column(type: 'string', length: 255, nullable: true)]
        private $emailText;

        #[ORM\OneToMany(mappedBy: 'user', targetEntity: Client::class)]
        private $clients;

        #[ORM\OneToMany(mappedBy: 'user', targetEntity: DeliveryNote::class, orphanRemoval: true)]
        private $deliveryNotes;

        #[ORM\Column(type: 'json')]
        private $roles = [];

        #[ORM\Column(type: 'string', length: 255)]
        private $password;

        private $plainPassword;

        public function __construct()
        {
            $this->clients = new ArrayCollection();
            $this->deliveryNotes = new ArrayCollection();
            parent::__construct();
        }

        public function getEmailText()
        {
            return $this->emailText;
        }

        public function setEmailText($emailText): void
        {
            $this->emailText = $emailText;
        }

        public function __toString(): string
        {
            return $this->getEmail();
        }


        public function getId(): ?int
        {
            return parent::getId();
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

        public function getLogo()
        {
            return $this->logo;
        }

        public function getLogoUrl(): ?string
        {
            if (!$this->logo) {
                return null;
            }

            if (strpos($this->logo, '/') !== false) {
                return $this->logo;
            }

            return sprintf('/uploads/logos/%s', $this->logo);
        }

        public function setLogo(?string $logo): void
        {
            $this->logo = $logo;
        }

        public function getFooter(): ?string
        {
            return $this->footer;
        }

        public function setFooter(?string $footer): self
        {
            $this->footer = $footer;

            return $this;
        }

        /**
         * @return Collection<int, Client>
         */
        public function getClients(): Collection
        {
            return $this->clients;
        }

        public function addClient(Client $client): self
        {
            if (!$this->clients->contains($client)) {
                $this->clients[] = $client;
                $client->setUser($this);
            }

            return $this;
        }

        public function removeClient(Client $client): self
        {
            if ($this->clients->removeElement($client) && $client->getUser() === $this) {
                    $client->setUser(null);
            }

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
                $deliveryNote->setUser($this);
            }

            return $this;
        }

        public function removeDeliveryNote(DeliveryNote $deliveryNote): self
        {
            if ($this->deliveryNotes->removeElement($deliveryNote) && $deliveryNote->getUser() === $this) {
                    $deliveryNote->setUser(null);
            }

            return $this;
        }

        /**
         * A visual identifier that represents this user.
         *
         * @see UserInterface
         */
        public function getUserIdentifier(): string
        {
            return (string)$this->getEmail();
        }

        /**
         * @deprecated since Symfony 5.3, use getUserIdentifier instead
         */
        public function getUsername(): string
        {
            return (string)$this->getEmail();
        }

        /**
         * @see UserInterface
         */
        public function getRoles(): array
        {
            $roles = $this->roles;
            // guarantee every user at least has ROLE_USER
            $roles[] = 'ROLE_USER';

            return array_unique($roles);
        }

        public function setRoles(array $roles): self
        {
            $this->roles = $roles;

            return $this;
        }


        /**
         * This method can be removed in Symfony 6.0 - is not needed for apps that do not check user passwords.
         *
         * @see UserInterface
         */
        public function getSalt(): ?string
        {
            return null;
        }

        /**
         * @see UserInterface
         */
        public function eraseCredentials()
        {
            // If you store any temporary, sensitive data on the user, clear it here
            $this->plainPassword = null;
        }

        /**
         * @see PasswordAuthenticatedUserInterface
         */
        public function getPassword(): ?string
        {
            return $this->password;
        }

        public function setPassword(string $password): self
        {
            $this->password = $password;

            return $this;
        }

        public function getPlainPassword(): ?string
        {
            return $this->plainPassword;
        }

        public function setPlainPassword($plainPassword): void
        {
            $this->plainPassword = $plainPassword;
        }
    }
