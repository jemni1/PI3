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
    #[Assert\NotNull(message: "La date de recyclage est obligatoire.")]
    private ?\DateTimeInterface $dateRecyclage = null;

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
    #[ORM\OneToMany(targetEntity: CollecteDechet::class, mappedBy: 'recyclageDechet')]
    private Collection $collecte;

    public function __construct()
    {
        $this->collecte = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateRecyclage(): ?\DateTimeInterface
    {
        return $this->dateRecyclage;
    }

    public function setDateRecyclage(?\DateTimeInterface $dateRecyclage): static
    {
        $this->dateRecyclage = $dateRecyclage;
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
    public function getCollecte(): Collection
    {
        return $this->collecte;
    }

    public function addCollecte(CollecteDechet $collecte): static
    {
        if (!$this->collecte->contains($collecte)) {
            $this->collecte->add($collecte);
            $collecte->setRecyclageDechet($this);
        }

        return $this;
    }

    public function removeCollecte(CollecteDechet $collecte): static
    {
        if ($this->collecte->removeElement($collecte)) {
            if ($collecte->getRecyclageDechet() === $this) {
                $collecte->setRecyclageDechet(null);
            }
        }

        return $this;
    }
}
