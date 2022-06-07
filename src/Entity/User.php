<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AttributeOverride;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
/**
 * @AttributeOverride(name="email",
 *          column=@Column(
 *              name     = "email",
 *              type     = "string",
 *              nullable = true,
 *              unique   = true,
 *              length   = 180
 *          )
 *      )
 *
 */
class User extends Business implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'blob', nullable: true)]
    private $logo;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $footer;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Client::class)]
    private $clients;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DeliveryNote::class, orphanRemoval: true)]
    private $deliveryNotes;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string', length: 255)]
    private $password;

    private $plainPassword;

    /*#[ORM\OneToMany(mappedBy: "client", targetEntity: Client::class)]
    private $clients;*/

    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->deliveryNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo): self
    {
        $this->logo = $logo;

        return $this;
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
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getUser() === $this) {
                $client->setUser(null);
            }
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
        if ($this->deliveryNotes->removeElement($deliveryNote)) {
            // set the owning side to null (unless already changed)
            if ($deliveryNote->getUser() === $this) {
                $deliveryNote->setUser(null);
            }
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
        return (string) parent::getEmail();
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) parent::getEmail();
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
