<?php

namespace App\Entity;

use App\Repository\TerrainsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TerrainsRepository::class)]
class Terrains
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column]
    private ?int $superficie = null;

    #[ORM\Column(length: 255)]
    private ?string $culture = null;

    /**
     * @var Collection<int, CollecteDechet>
     */
    #[ORM\OneToMany(targetEntity: CollecteDechet::class, mappedBy: 'id_terrain')]
    private Collection $collecteDechets;

    public function __construct()
    {
        $this->collecteDechets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getSuperficie(): ?int
    {
        return $this->superficie;
    }

    public function setSuperficie(int $superficie): static
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getCulture(): ?string
    {
        return $this->culture;
    }

    public function setCulture(string $culture): static
    {
        $this->culture = $culture;

        return $this;
    }

    /**
     * @return Collection<int, CollecteDechet>
     */
    public function getCollecteDechets(): Collection
    {
        return $this->collecteDechets;
    }

    public function addCollecteDechet(CollecteDechet $collecteDechet): static
    {
        if (!$this->collecteDechets->contains($collecteDechet)) {
            $this->collecteDechets->add($collecteDechet);
            $collecteDechet->setIdTerrain($this);
        }

        return $this;
    }

    public function removeCollecteDechet(CollecteDechet $collecteDechet): static
    {
        if ($this->collecteDechets->removeElement($collecteDechet)) {
            // set the owning side to null (unless already changed)
            if ($collecteDechet->getIdTerrain() === $this) {
                $collecteDechet->setIdTerrain(null);
            }
        }

        return $this;
    }
}
