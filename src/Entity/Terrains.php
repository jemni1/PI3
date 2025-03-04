<?php

namespace App\Entity;

use App\Repository\TerrainsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Contracts\HttpClient\HttpClientInterface;

#[ORM\Entity(repositoryClass: TerrainsRepository::class)]
class Terrains
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du terrain ne peut pas être vide.")]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse est obligatoire.")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "L'adresse doit contenir au moins {{ limit }} caractères.",
        maxMessage: "L'adresse ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $adresse = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La superficie est obligatoire.")]
    #[Assert\Positive(message: "La superficie doit être un nombre positif.")]
    #[Assert\LessThan(
        value: 100000,
        message: "La superficie ne doit pas dépasser {{ compared_value }} m²."
    )]
    private ?int $superficie = null;

    #[ORM\Column(type: "float")]
    #[Assert\NotBlank(message: "La latitude est obligatoire.")]
    #[Assert\Type(type: 'float', message: 'La latitude doit être un nombre décimal.')]
    private ?float $latitude = null;
    
    #[ORM\Column(type: "float")]
    #[Assert\NotBlank(message: "La longitude est obligatoire.")]
    #[Assert\Type(type: 'float', message: 'La longitude doit être un nombre décimal.')]
    private ?float $longitude = null;


    #[ORM\Column(length: 255)]
    #[Assert\Image(
        maxSize: "2M",
        mimeTypes: ["image/jpeg", "image/png", "image/webp"],
        mimeTypesMessage: "Seuls les formats JPEG, PNG et WEBP sont autorisés.",
        maxSizeMessage: "L'image ne doit pas dépasser 2 Mo."
    )]
    private ?string $image = null;

    /**
     * @var Collection<int, Culture>
     */
    #[ORM\ManyToMany(targetEntity: Culture::class, inversedBy: 'terrains')]
#[ORM\JoinTable(name: "terrain_culture")]
#[ORM\JoinColumn(name: "terrain_id", referencedColumnName: "id")]
#[ORM\InverseJoinColumn(name: "culture_id", referencedColumnName: "id_culture")]
private Collection $cultures;
    /**
     * @var Collection<int, CollecteDechet>
     */
    #[ORM\OneToMany(targetEntity: CollecteDechet::class, mappedBy: 'id_terrain')]
    private Collection $collecteDechets;



    public function __construct()
    {
        $this->cultures = new ArrayCollection();
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

   
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }
    
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }
   
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Culture>
     */
    public function getCultures(): Collection
    {
        return $this->cultures;
    }

    public function addCulture(Culture $culture): static
    {
        if (!$this->cultures->contains($culture)) {
            $this->cultures->add($culture);
        }

        return $this;
    }

    public function removeCulture(Culture $culture): static
    {
        $this->cultures->removeElement($culture);

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

    public function getWeatherData(HttpClientInterface $httpClient, string $apiKey = 'd572ac8061554b38bdf134529250203'): array
    {
        if (!$this->latitude || !$this->longitude) {
            throw new \LogicException('Les coordonnées sont nécessaires pour récupérer les données météo');
        }
        
        $url = sprintf(
            'http://api.weatherapi.com/v1/current.json?key=%s&q=%s,%s',
            $apiKey,
            $this->latitude,
            $this->longitude
        );
        
        try {
            $response = $httpClient->request('GET', $url);
            return $response->toArray();
        } catch (\Exception $e) {
            // Journalisez l'erreur ou gérez-la selon vos besoins
            return ['error' => $e->getMessage()];
        }
    }
   

   
}
