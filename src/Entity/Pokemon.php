<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Table(name: 'pokemon')]
#[ORM\UniqueConstraint(name: 'numero_national', columns: ['numero_national'])]
#[ORM\Entity(repositoryClass: PokemonRepository::class)]
class Pokemon
{
    #[ORM\Column]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero_national = null;

    #[ORM\Column]
    private ?int $generation = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image_url = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $taille = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $poids = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $criUrl = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $pv = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $attaque = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $attaqueSpe = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $defense = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $defenseSpe = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $vitesse = null;

    #[ORM\OneToMany(mappedBy: "pokemonDebut", targetEntity: Evolution::class)]
    private Collection $evolutionsDepuis;

    #[ORM\OneToMany(mappedBy: "pokemonFin", targetEntity: Evolution::class)]
    private Collection $evolutionsVers;

    #[ORM\OneToMany(mappedBy: "pokemon", targetEntity: PokemonType::class)]
    private Collection $types;

    /**
     * @var Collection<int, PokemonTalent>
     */
    #[ORM\OneToMany(targetEntity: PokemonTalent::class, mappedBy: 'pokemon')]
    private Collection $pokemonTalent;

    public function __construct()
    {
        $this->evolutionsDepuis = new ArrayCollection();
        $this->evolutionsVers = new ArrayCollection();
        $this->types = new ArrayCollection();
        $this->pokemonTalent = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroNational(): ?int
    {
        return $this->numero_national;
    }

    public function setNumeroNational(int $numero_national): static
    {
        $this->numero_national = $numero_national;

        return $this;
    }

    public function getGeneration(): ?int
    {
        return $this->generation;
    }

    public function setGeneration(int $generation): static
    {
        $this->generation = $generation;

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

    public function getImageUrl(): ?string
    {
        return $this->image_url;
    }

    public function setImageUrl(?string $image_url): static
    {
        $this->image_url = $image_url;

        return $this;
    }
    public function getTypes(): Collection
    {
        return $this->types;
    }
    public function getEvolutionsDepuis(): Collection
    {
        return $this->evolutionsDepuis;
    }

    public function getPokemonTalent(): Collection
    {
        return $this->pokemonTalent;
    }

    public function getTaille(): ?float {
        return $this->taille;
    }

    public function setTaille(?float $taille): static {
        $this->taille = $taille;
        return $this;
    }

    public function getPoids(): ?float {
        return $this->poids;
    }

    public function setPoids(?float $poids): static {
        $this->poids = $poids;
        return $this;
    }

    public function getCriurl(): ?string {
        return $this->criUrl;
    }

    public function setCriurl(?string $criUrl): static {
        $this->criUrl = $criUrl;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): static {
        $this->description = $description;
        return $this;
    }

    public function getPv(): ?int {
        return $this->pv;
    }

    public function setPv(?int $pv): static {
        $this->pv = $pv;
        return $this;
    }

    public function getAttaque(): ?int {
        return $this->attaque;
    }

    public function setAttaque(?int $attaque): static {
        $this->attaque = $attaque;
        return $this;
    }

    public function getAttaquespe(): ?int {
        return $this->attaqueSpe;
    }

    public function setAttaquespe(?int $attaqueSpe): static {
        $this->attaqueSpe = $attaqueSpe;
        return $this;
    }

    public function getDefense(): ?int {
        return $this->defense;
    }

    public function setDefense(?int $defense): static {
        $this->defense = $defense;
        return $this;
    }

    public function getDefensespe(): ?int {
        return $this->defenseSpe;
    }

    public function setDefensespe(?int $defenseSpe): static {
        $this->defenseSpe = $defenseSpe;
        return $this;
    }

    public function getVitesse(): ?int {
        return $this->vitesse;
    }

    public function setVitesse(?int $vitesse): static {
        $this->vitesse = $vitesse;
        return $this;
    }
}
