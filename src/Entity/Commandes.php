<?php

namespace App\Entity;

use App\Repository\CommandesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CommandesRepository::class)]
class Commandes
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
    #[ORM\Column]
    private ?int $id_client = null;
    #[ORM\Column]
    private ?int $id_produit = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut ne peut pas être vide.")]
    #[Assert\Length(max: 255, maxMessage: "Le statut ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

  
    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix ne peut pas être vide.")]
    #[Assert\Type(type: "integer", message: "Le prix doit être un nombre entier.")]
    #[Assert\GreaterThan(value: 0, message: "Le prix doit être supérieur à zéro.")]
    private ?int $prix = null;

    #[ORM\Column(length: 90)]
    #[Assert\NotBlank(message: "Le nom du produit ne peut pas être vide.")]
    #[Assert\Length(max: 90, maxMessage: "Le nom du produit ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $nom = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }
    public function getId_client(): ?int
    {
        return $this->id_client;
    }

    public function setId_client(int $id_client): static
    {
        $this->id_client = $id_client;

        return $this;
    }
    public function getId_produit(): ?int
    {
        return $this->id_produit;
    }

    public function setId_produit(int $id_produit): static
    {
        $this->id_produit = $id_produit;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

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
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
}
