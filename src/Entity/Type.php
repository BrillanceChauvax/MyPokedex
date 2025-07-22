<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Table(name: 'type')]
#[ORM\UniqueConstraint(name: 'nom', columns: ['nom'])]
#[ORM\Entity(repositoryClass: TypeRepository::class)]
class Type
{
    #[ORM\Column]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $icone_url = null;

    #[ORM\OneToMany(mappedBy: "type", targetEntity: PokemonType::class)]
    private Collection $pokemons;

    public function __construct()
    {
        $this->pokemons = new ArrayCollection();
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

    public function getIconeUrl(): ?string
    {
        return $this->icone_url;
    }

    public function setIconeUrl(?string $icone_url): static
    {
        $this->icone_url = $icone_url;

        return $this;
    }
}
