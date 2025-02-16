<?php

namespace App\Entity;

use App\Repository\CollecteDechetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CollecteDechetRepository::class)]
class CollecteDechet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type de déchet est obligatoire.")]
    private ?string $typeDechet = null;

    #[ORM\Column(type: "float")]
    #[Assert\NotNull(message: "La quantité est obligatoire.")]
    #[Assert\Positive(message: "La quantité doit être un nombre positif.")]
    #[Assert\Type(
        type: "numeric",
        message: "La quantité doit être un nombre."
    )]
    private ?float $quantite = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "La date de début est obligatoire.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date de début ne peut pas être antérieure à aujourd’hui.")]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotNull(message: "La date de fin est obligatoire.")]
    #[Assert\GreaterThan(propertyPath: "dateDebut", message: "La date de fin doit être postérieure à la date de début.")]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\ManyToOne(targetEntity: RecyclageDechet::class, inversedBy: "collectes")]
    private ?RecyclageDechet $recyclageDechet = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imageUrl = null;

public function getImageUrl(): ?string
{
    return $this->imageUrl;
}

public function setImageUrl(?string $imageUrl): static
{
    $this->imageUrl = $imageUrl;
    return $this;
}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeDechet(): ?string
    {
        return $this->typeDechet;
    }

    public function setTypeDechet(?string $typeDechet): static
    {
        $this->typeDechet = $typeDechet;
        return $this;
    }

    public function getQuantite(): ?float
    {
        return $this->quantite;
    }

    public function setQuantite(?float $quantite): static
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getRecyclageDechet(): ?RecyclageDechet
    {
        return $this->recyclageDechet;
    }

    public function setRecyclageDechet(?RecyclageDechet $recyclageDechet): static
    {
        $this->recyclageDechet = $recyclageDechet;
        return $this;
    }
}
