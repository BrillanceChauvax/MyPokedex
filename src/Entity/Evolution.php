<?php

namespace App\Entity;

use App\Repository\EvolutionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'evolution')]
#[ORM\Index(name: 'pokemon_debut_id', columns: ['pokemon_debut_id'])]
#[ORM\Index(name: 'pokemon_fin_id', columns: ['pokemon_fin_id'])]
#[ORM\Entity(repositoryClass: EvolutionRepository::class)]
class Evolution
{
    #[ORM\Column]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Pokemon::class, inversedBy: "evolutionsDepuis")]
    #[ORM\JoinColumn(name: "pokemon_debut_id", referencedColumnName: "id")]
    private ?Pokemon $pokemonDebut = null;

    #[ORM\ManyToOne(targetEntity: Pokemon::class, inversedBy: "evolutionsVers")]
    #[ORM\JoinColumn(name: "pokemon_fin_id", referencedColumnName: "id")]
    private ?Pokemon $pokemonFin = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $condition = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPokemonDebutId(): ?int
    {
        return $this->pokemonDebut ? $this->pokemonDebut->getId() : null;
    }

    public function setPokemonDebutId(int $pokemon_debut_id): static
    {
        $this->pokemon_debut_id = $pokemon_debut_id;

        return $this;
    }

    public function getPokemonFinId(): ?int
    {
        return $this->pokemonFin ? $this->pokemonFin->getId() : null;
    }

    public function setPokemonFinId(int $pokemon_fin_id): static
    {
        $this->pokemon_fin_id = $pokemon_fin_id;

        return $this;
    }

    public function setPokemonDebut(?Pokemon $pokemonDebut): static
    {
        $this->pokemonDebut = $pokemonDebut;
        return $this;
    }

    public function getPokemonDebut(): ?Pokemon
    {
        return $this->pokemonDebut;
    }

    public function setPokemonFin(?Pokemon $pokemonFin): static
    {
        $this->pokemonFin = $pokemonFin;
        return $this;
    }

    public function getPokemonFin(): ?Pokemon
    {
        return $this->pokemonFin;
    }

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function setCondition(?string $condition): static
    {
        $this->condition = $condition;
        return $this;
    }
}
