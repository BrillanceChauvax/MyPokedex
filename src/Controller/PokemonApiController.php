<?php

namespace App\Controller;

use App\Repository\PokemonRepository;
use App\Repository\EvolutionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\PokemonTalent;

class PokemonApiController extends AbstractController
{
    #[Route('/api/pokemon/{id}', name: 'pokemon_api_detail', methods: ['GET'])]
    public function getPokemonDetail(
        int $id,
        PokemonRepository $pokemonRepository,
        EvolutionRepository $evolutionRepository
    ): JsonResponse {
        $pokemon = $pokemonRepository->find($id);
        
        if (!$pokemon) {
            return new JsonResponse(['error' => 'Pokémon non trouvé'], 404);
        }

        // Récupération des évolutions complètes
        $evolutionChain = $this->buildEvolutionChain($pokemon, $evolutionRepository, $pokemonRepository);

        $pokemonData = [
            'id' => $pokemon->getId(),
            'numero_national' => $pokemon->getNumeroNational(),
            'nom' => $pokemon->getNom(),
            'generation' => $pokemon->getGeneration(),
            'image_url' => $pokemon->getImageUrl(),
            'types' => [],
            'evolution_chain' => $evolutionChain,
            'taille' => $pokemon->getTaille(),
            'poids' => $pokemon->getPoids(),
            'cri_url' => $pokemon->getCriUrl(),
            'description' => $pokemon->getDescription(),
            'stats' => [
            'pv' => $pokemon->getPv(),
            'attaque' => $pokemon->getAttaque(),
            'attaque_spe' => $pokemon->getAttaqueSpe(),
            'defense' => $pokemon->getDefense(),
            'defense_spe' => $pokemon->getDefenseSpe(),
            'vitesse' => $pokemon->getVitesse(),
            ],
            'talents' => array_map(function(PokemonTalent $pt) {
                return [
                    'nom' => $pt->getTalent()->getNom(),
                    'description' => $pt->getTalent()->getDescription(),
                    'is_hidden' => $pt->isHidden(),
                ];
            }, $pokemon->getPokemonTalent()->toArray()),
        ];

        // Récupération des types via la relation PokemonType
        foreach ($pokemon->getTypes() as $pokemonType) {
            $type = $pokemonType->getType();
            $pokemonData['types'][] = [
                'id' => $type->getId(),
                'nom' => $type->getNom(),
                'icone_url' => $type->getIconeUrl()
            ];
        }

        return new JsonResponse($pokemonData);
    }

    private function buildEvolutionChain(
        $pokemon,
        EvolutionRepository $evolutionRepository,
        PokemonRepository $pokemonRepository
    ): array {
        $startPokemon = $this->findChainStart($pokemon, $evolutionRepository, $pokemonRepository);
        
        $chain = [];
        $processed = []; 
        $this->buildChainRecursive($startPokemon, $evolutionRepository, $pokemonRepository, $chain, $processed);

        return $chain;
    }


    private function findChainStart($pokemon, $evolutionRepository, $pokemonRepository)
    {
        // Chercher s'il y a une pré-évolution
        $preEvolution = $evolutionRepository->findOneBy(['pokemonFin' => $pokemon]);
        
        if ($preEvolution) {
            return $this->findChainStart($preEvolution->getPokemonDebut(), $evolutionRepository, $pokemonRepository);
        }
        
        return $pokemon;
    }

    private function buildChainRecursive($pokemon, $evolutionRepository, $pokemonRepository, &$chain, &$processed, ?string $conditionFromPrev = null)
    {
        if (in_array($pokemon->getId(), $processed)) {
            return;
        }

        $processed[] = $pokemon->getId();
        
        $pokemonData = [
            'id' => $pokemon->getId(),
            'numero_national' => $pokemon->getNumeroNational(),
            'nom' => $pokemon->getNom(),
            'generation' => $pokemon->getGeneration(),
            'image_url' => $pokemon->getImageUrl(),
            'condition' => $conditionFromPrev,
            'types' => []
        ];

        // Récupération des types
        foreach ($pokemon->getTypes() as $pokemonType) {
            $type = $pokemonType->getType();
            $pokemonData['types'][] = [
                'id' => $type->getId(),
                'nom' => $type->getNom(),
                'icone_url' => $type->getIconeUrl()
            ];
        }

        $chain[] = $pokemonData;

        // Chercher les évolutions suivantes
        $evolutions = $evolutionRepository->findBy(['pokemonDebut' => $pokemon]);
        
        foreach ($evolutions as $evolution) {
            $nextPokemon = $evolution->getPokemonFin();
            if ($nextPokemon) {
                $this->buildChainRecursive($nextPokemon, $evolutionRepository, $pokemonRepository, $chain, $processed, $evolution->getCondition());
            }
        }
    }
}
