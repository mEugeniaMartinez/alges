<?php

namespace App\Entity;

use App\Repository\DeliveryNoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeliveryNoteRepository::class)]
class DeliveryNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date', nullable: true)]
    private $date;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $material;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $faultDescription;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $intervention;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $number;

    #[ORM\Column(type: 'blob', nullable: true)]
    private $signature;

    #[ORM\Column(type: 'blob', nullable: true)]
    private $pdf;

    #[ORM\Column(type: 'string', length: 255)]
    private $state;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'deliveryNotes')]
    private $client;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'deliveryNotes')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    /**
     * @param $state
     * @param $user
     */
    public function __construct(string $state, User $user)
    {
        $this->state = $state;
        $this->user = $user;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMaterial(): ?string
    {
        return $this->material;
    }

    public function setMaterial(?string $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getFaultDescription(): ?string
    {
        return $this->faultDescription;
    }

    public function setFaultDescription(?string $faultDescription): self
    {
        $this->faultDescription = $faultDescription;

        return $this;
    }

    public function getIntervention(): ?string
    {
        return $this->intervention;
    }

    public function setIntervention(?string $intervention): self
    {
        $this->intervention = $intervention;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function setSignature($signature): self
    {
        $this->signature = $signature;

        return $this;
    }

    public function getPdf()
    {
        return $this->pdf;
    }

    public function setPdf($pdf): self
    {
        $this->pdf = $pdf;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
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
}
