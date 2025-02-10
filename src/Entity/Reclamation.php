<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Reponse;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    //#[ORM\JoinColumn(name:'id',referencedColumnName:'id')]
    private ?int $Id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le sujet ne peut pas être vide.")]
    #[Assert\Length(min: 5, minMessage: "Le sujet doit contenir au moins {{ limit }} caractères.")]
    private ?string $sujet = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide.")]
    #[Assert\Length(min: 10, minMessage: "La description doit contenir au moins {{ limit }} caractères.")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut ne peut pas être vide.")]
    private ?string $status = null;

   

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de réclamation ne peut pas être vide.")]
    private ?\DateTimeInterface $daterec = null;

    public function getId(): ?int
    {
        return $this->Id;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(?string $sujet): static
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

  
    public function getDaterec(): ?\DateTimeInterface
    {
        return $this->daterec;
    }

    public function setDaterec(\DateTimeInterface $daterec): static
    {
        $this->daterec = $daterec;

        return $this;
    }
    public function getResponse(): ?Reponse
    {
        return $this->response;
    }

    public function setResponse(?Reponse $response): self
    {
        $this->response = $response;

        return $this;
    }
    public function __construct()
    {
        // Set the default date to the current date when the entity is created
        $this->daterec = new \DateTime(); 
        $this->status = 'En attente'; 
    }


}
