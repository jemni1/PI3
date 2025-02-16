<?php

namespace App\Entity;

use App\Repository\CultureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CultureRepository::class)]
class Culture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_culture")]
    private ?int $id_culture = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $periodePlantation = null;

    #[ORM\Column(name: "periode_recoltation", length: 255)]
private ?string $periodeRecolte = null;

    #[ORM\Column(length: 255)]
    private ?int $besoinEau = null;

    /**
     * @var Collection<int, Terrains>
     */
    #[ORM\ManyToMany(targetEntity: Terrains::class, mappedBy: 'cultures')]
    private Collection $terrains;

    public function __construct()
    {
        $this->terrains = new ArrayCollection();
    }

  

  
    public function getId(): ?int
    {
        return $this->id_culture;
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

    public function getPeriodePlantation(): ?string
    {
        return $this->periodePlantation;
    }

    public function setPeriodePlantation(string $periodePlantation): static
    {
        $this->periodePlantation = $periodePlantation;

        return $this;
    }

    public function getPeriodeRecolte(): ?string
    {
        return $this->periodeRecolte;
    }

    public function setPeriodeRecolte(string $periodeRecolte): static
    {
        $this->periodeRecolte = $periodeRecolte;

        return $this;
    }

    public function getBesoinEau(): ?string
    {
        return $this->besoinEau;
    }

    public function setBesoinEau(int $besoinEau): static
    {
        $this->besoinEau = $besoinEau;

        return $this;
    }

    /**
     * @return Collection<int, Terrains>
     */
    public function getTerrains(): Collection
    {
        return $this->terrains;
    }

    public function addTerrain(Terrains $terrain): static
    {
        if (!$this->terrains->contains($terrain)) {
            $this->terrains->add($terrain);
            $terrain->addCulture($this);
        }

        return $this;
    }

    public function removeTerrain(Terrains $terrain): static
    {
        if ($this->terrains->removeElement($terrain)) {
            $terrain->removeCulture($this);
        }

        return $this;
    }

   
   

   
}
