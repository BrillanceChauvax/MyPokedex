<?php

namespace App\Entity;

use App\Repository\TalentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TalentRepository::class)]
class Talent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, PokemonTalent>
     */
    #[ORM\OneToMany(targetEntity: PokemonTalent::class, mappedBy: 'talent')]
    private Collection $pokemonTalent;

    public function __construct()
    {
        $this->pokemonTalent = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPokemonTalent(): Collection
    {
        return $this->pokemonTalent;
    }

    public function addPokemonTalent(PokemonTalent $pokemonTalent): static
    {
        if (!$this->pokemonTalent->contains($pokemonTalent)) {
            $this->pokemonTalent->add($pokemonTalent);
            $pokemonTalent->setTalent($this);
        }

        return $this;
    }

    public function removePokemonTalent(PokemonTalent $pokemonTalent): static
    {
        if ($this->pokemonTalent->removeElement($pokemonTalent)) {
            // set the owning side to null (unless already changed)
            if ($pokemonTalent->getTalent() === $this) {
                $pokemonTalent->setTalent(null);
            }
        }

        return $this;
    }
}
