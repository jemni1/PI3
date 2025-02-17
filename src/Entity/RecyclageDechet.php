<?php
namespace App\Entity;

use App\Repository\RecyclageDechetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecyclageDechetRepository::class)]
class RecyclageDechet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "La date de début est obligatoire.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date de début ne peut pas être antérieure à aujourd’hui.")]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: "La date de fin est obligatoire.")]
    #[Assert\GreaterThan(propertyPath: "dateDebut", message: "La date de fin doit être après la date de début.")]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: "float")]
    #[Assert\NotNull(message: "Veuillez saisir une quantité recyclée.")]
    #[Assert\Positive(message: "La quantité recyclée doit être un nombre positif.")]
    private ?float $quantiteRecyclee = null;

    #[ORM\Column(type: "float")]
    #[Assert\NotNull(message: "Veuillez saisir l'énergie produite.")]
    #[Assert\PositiveOrZero(message: "L'énergie produite ne peut pas être négative.")]
    private ?float $energieProduite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "Veuillez saisir l'utilisation de l'énergie.")]
    private ?string $utilisation = null;
    
    /**
     * @var Collection<int, CollecteDechet>
     */
    #[ORM\OneToMany(mappedBy: "recyclageDechet", targetEntity: CollecteDechet::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $collectes;

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


    public function __construct()
    {
        $this->collectes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuantiteRecyclee(): ?float
    {
        return $this->quantiteRecyclee;
    }

    public function setQuantiteRecyclee(?float $quantiteRecyclee): static
    {
        $this->quantiteRecyclee = $quantiteRecyclee;
        return $this;
    }

    public function getEnergieProduite(): ?float
    {
        return $this->energieProduite;
    }

    public function setEnergieProduite(?float $energieProduite): static
    {
        $this->energieProduite = $energieProduite;
        return $this;
    }

    public function getUtilisation(): ?string
    {
        return $this->utilisation;
    }

    public function setUtilisation(?string $utilisation): static
    {
        $this->utilisation = $utilisation;
        return $this;
    }

    /**
     * @return Collection<int, CollecteDechet>
     */
    public function getCollectes(): Collection
    {
        return $this->collectes;
    }

    public function addCollecte(CollecteDechet $collecte): static
    {
        if (!$this->collectes->contains($collecte)) {
            $this->collectes->add($collecte);
            $collecte->setRecyclageDechet($this);
        }

        return $this;
    }

    public function removeCollecte(CollecteDechet $collecte): static
    {
        if ($this->collectes->removeElement($collecte)) {
            if ($collecte->getRecyclageDechet() === $this) {
                $collecte->setRecyclageDechet(null);
            }
        }

        return $this;
    }
}
