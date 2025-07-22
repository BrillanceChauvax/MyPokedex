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

    #[ORM\OneToMany(mappedBy: "pokemonDebut", targetEntity: Evolution::class)]
    private Collection $evolutionsDepuis;

    #[ORM\OneToMany(mappedBy: "pokemonFin", targetEntity: Evolution::class)]
    private Collection $evolutionsVers;

    #[ORM\OneToMany(mappedBy: "pokemon", targetEntity: PokemonType::class)]
    private Collection $types;

    public function __construct()
    {
        $this->evolutionsDepuis = new ArrayCollection();
        $this->evolutionsVers = new ArrayCollection();
        $this->types = new ArrayCollection();
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
}
