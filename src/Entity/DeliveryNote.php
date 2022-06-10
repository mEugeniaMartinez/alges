<?php

namespace App\Entity;

use App\Repository\DeliveryNoteRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Symfony\Component\Validator\Constraints\Date;

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

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    private $number;

    #[ORM\Column(type: 'blob', nullable: true)]
    private $signature;

    #[ORM\Column(type: 'blob', nullable: true)]
    private $pdf;

    #[ORM\ManyToOne(targetEntity: Client::class,
        inversedBy: 'deliveryNotes')]
    private $client;

    #[ORM\ManyToOne(targetEntity: User::class,
        cascade: ['persist', 'merge'],
        inversedBy: 'deliveryNotes')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'boolean')]
    private $signed;

    #[ORM\Column(type: 'boolean')]
    private $completed;

    #[ORM\Column(type: 'boolean')]
    private $disabled;

    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    private $timeSpent;

    public function __construct()
    {
        //$this->setUser($_SESSION['user_data']);
        $this->signed = false;
        $this->disabled = false;
        $this->completed = false;
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

    public function setNumber(?string $id)
    {
        $this->number = $this->generateNumber($id);

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

    public function isSigned(): ?bool
    {
        return $this->signed;
    }

    public function setSigned(bool $signed): self
    {
        $this->signed = $signed;

        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function isDisabled(): ?bool
    {
        return $this->disabled;
    }

    public function setDisabled(bool $disabled): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    private function generateNumber(?string $id): string
    {
        $date = date("my", strtotime("today"));; // 06 Jun, 2022 -> 0622
        return 'AL_' . str_pad($id . $date, 7,
            "0", STR_PAD_LEFT); //AL_0020622
    }

    public function getTimeSpent(): ?string
    {
        return $this->timeSpent;
    }

    public function setTimeSpent(?string $timeSpent): self
    {
        $this->timeSpent = $timeSpent;

        return $this;
    }
}
