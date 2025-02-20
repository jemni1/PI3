<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Reclamation;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ORM\JoinColumn(name:'idreponse',referencedColumnName:'id')]
    private ?int $idReponse= null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le message ne peut pas être vide.")]
    #[Assert\Length(min: 5, minMessage: "Le message doit contenir au moins {{ limit }} caractères.")]

    private ?string $message = null;

 
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $daterep = null;

 #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Reclamation $idreclamation = null;

    public function __construct()
    {
        $this->daterep = new \DateTime();
        $this->reclamations = new ArrayCollection();
    }

    public function getIdReponse(): ?int
    {
        return $this->idReponse;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    
    public function getDaterep(): ?\DateTimeInterface
{
    return $this->daterep;
}

    public function setDaterep(\DateTimeInterface $daterep): static
    {
        $this->daterep = $daterep;

        return $this;
    }

    public function getIdreclamation(): ?reclamation
    {
        return $this->idreclamation;
    }

    public function setIdreclamation(?reclamation $idreclamation): static
    {
        $this->idreclamation = $idreclamation;

        return $this;
    }
}
