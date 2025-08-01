<?php

namespace App\Entity;

use App\Repository\PokemonTalentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonTalentRepository::class)]
class PokemonTalent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pokemonTalent')]
    private ?Pokemon $pokemon = null;

    #[ORM\ManyToOne(inversedBy: 'pokemonTalent')]
    private ?Talent $talent = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isHidden = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPokemon(): ?Pokemon
    {
        return $this->pokemon;
    }

    public function setPokemon(?Pokemon $pokemon): static
    {
        $this->pokemon = $pokemon;

        return $this;
    }

    public function getTalent(): ?Talent
    {
        return $this->talent;
    }

    public function setTalent(?Talent $talent): static
    {
        $this->talent = $talent;

        return $this;
    }

    public function isHidden(): ?bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(?bool $isHidden): static
    {
        $this->isHidden = $isHidden;

        return $this;
    }
}
