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
    #[Assert\NotNull(message: "La date de collecte ne peut pas être vide.")]
    private ?\DateTimeInterface $dateCollecte = null;
    
    
    private ?Terrains $id_terrain = null;

    #[ORM\ManyToOne(inversedBy: 'collecte')]
    private ?RecyclageDechet $recyclageDechet = null;

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

    public function getDateCollecte(): ?\DateTimeInterface
    {
        return $this->dateCollecte;
    }

    public function setDateCollecte(?\DateTimeInterface $dateCollecte): static
    {
        $this->dateCollecte = $dateCollecte; 
        return $this;
    }

    public function getIdTerrain(): ?Terrains
    {
        return $this->id_terrain;
    }

    public function setIdTerrain(?Terrains $id_terrain): static
    {
        $this->id_terrain = $id_terrain;
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
