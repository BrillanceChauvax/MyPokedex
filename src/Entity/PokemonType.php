<?php

namespace App\Entity;

use App\Repository\PokemonTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'pokemon_type')]
#[ORM\Index(name: 'type_id', columns: ['type_id'])]
#[ORM\Entity(repositoryClass: PokemonTypeRepository::class)]
class PokemonType
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Pokemon::class, inversedBy: "types")]
    #[ORM\JoinColumn(name: "pokemon_id", referencedColumnName: "id")]
    private ?Pokemon $pokemon = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Type::class, inversedBy: "pokemons")]
    #[ORM\JoinColumn(name: "type_id", referencedColumnName: "id")]
    private ?Type $type = null;

    public function getPokemonId(): ?int
    {
        return $this->pokemon;
    }

    public function setPokemonId(int $pokemon_id): static
    {
        $this->pokemon_id = $pokemon_id;

        return $this;
    }

    public function getTypeId(): ?int
    {
        return $this->type;
    }

    public function setTypeId(int $type_id): static
    {
        $this->type_id = $type_id;

        return $this;
    }
    public function getType(): ?Type
    {
        return $this->type;
    }
}
