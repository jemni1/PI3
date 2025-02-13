<?php

namespace App\Entity;

use App\Repository\ProduitsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: ProduitsRepository::class)]
class Produits
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La quantité ne peut pas être vide.")]
    #[Assert\Type(type: "integer", message: "La quantité doit être un nombre entier.")]
    #[Assert\GreaterThan(value: 0, message: "La quantité doit être supérieure à zéro.")]
    private ?int $quantite = null;

    #[ORM\Column(length: 90)]
    #[Assert\NotBlank(message: "Le nom du produit ne peut pas être vide.")]
    #[Assert\Length(max: 90, maxMessage: "Le nom du produit ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[Assert\NotNull(message: "Le terrain associé ne peut pas être vide.")]
    private ?Terrains $id_terrain = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix ne peut pas être vide.")]
    #[Assert\Type(type: "integer", message: "Le prix doit être un nombre entier.")]
    #[Assert\GreaterThan(value: 0, message: "Le prix doit être supérieur à zéro.")]
    private ?int $prix = null;
    
    #[ORM\Column(length: 255)]
    private ?string $image = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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





    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getImage(): ?string
{
    return $this->image;
}

public function setImage(?string $image): static
{
    $this->image = $image;
    return $this;
}
}
