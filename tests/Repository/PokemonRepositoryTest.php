<?php

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\PokemonRepository;

class PokemonRepositoryTest extends KernelTestCase
{
    private ?PokemonRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = static::getContainer()->get(PokemonRepository::class);
    }

    // Teste la récupération simple de Bulbizarre
    public function testFilterAllReturnsBulbizarre(): void
    {
        $result = $this->repository->filterAll(1, null, 'Bulbizarre');
        $this->assertNotEmpty($result, 'FilterAll should return results for Bulbizarre');
    }

    // Teste la récupération simple globale (sans filtre)
    // Vérifier que filterAll retourne bien des Pokémon.
    public function testFilterAllReturnsSomePokemon(): void
    {
        $result = $this->repository->filterAll(null, null, null);
        $this->assertNotEmpty($result);
    }

    // Tester la recherche par génération
    // Teste la filtration par génération, génération 1 renvoie au moins un résultat.
    public function testFilterByGeneration(): void
    {
        $result = $this->repository->filterAll(1, null, null);
        $this->assertNotEmpty($result);
        foreach ($result as $pokemon) {
            $this->assertEquals(1, $pokemon->getGeneration());
        }
    }

    // Teste la recherche par type
    // Tester la filtrage par un type connu, ex: type 'Feu' (id 2 selon base)
    public function testFilterByType(): void
    {
        $result = $this->repository->filterAll(null, 2, null);
        $this->assertNotEmpty($result);
        foreach ($result as $pokemon) {
            $types = $pokemon->getTypes()->map(fn($pt) => $pt->getType()->getId())->toArray();
            $this->assertContains(2, $types);
        }
    }

    // Teste la recherche par nom ou numéro
    // Recherche via une partie de nom, ex: "Bulb"
    public function testSearchByName(): void
    {
        $result = $this->repository->filterAll(null, null, 'Bulb');
        $this->assertNotEmpty($result);
        $this->assertStringContainsStringIgnoringCase('Bulb', $result[0]->getNom());
    }

    // Teste la pagination et tri
    // Vérifier résultats cohérents selon limite, page, ordre
    public function testFilterWithPagination(): void
    {
        $pages = $this->repository->filterAllWithPagination(null, null, null, 10, 1);
        $this->assertNotEmpty($pages['pokemons']);
        $this->assertIsArray($pages['pokemons']);
        $this->assertEquals(10, count($pages['pokemons']));
    }

    // Teste la récupération des générations distinctes
    // Assurer que les générations retournées sont un tableau non vide
    public function testFindAllGenerations(): void
    {
        $generations = $this->repository->findAllGenerations();
        $this->assertIsArray($generations);
        $this->assertNotEmpty($generations);
        $this->assertContains(1, $generations); 
    }
}
