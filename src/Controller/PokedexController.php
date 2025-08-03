<?php

namespace App\Controller;

use App\Repository\PokemonRepository;
use App\Repository\TypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PokedexController extends AbstractController
{
    #[Route('/', name: 'pokedex')]
    public function index(Request $request, PokemonRepository $pokemonRepository, TypeRepository $typeRepository)
    {
        $generation = $request->query->get('generation');
        $type = $request->query->get('type');
        $search = $request->query->get('q');
        $limit = $request->query->get('limit', 25); 
        $page = $request->query->get('page', 1); 
        $sort = $request->query->get('sort', 'numero');
        $order = $request->query->get('order', 'asc');
        
        $allowedSorts = ['generation', 'numero', 'nom', 'talents'];
        $sort = in_array($sort, $allowedSorts) ? $sort : 'numero';
        $order = in_array($order, ['asc', 'desc']) ? $order : 'asc';

        $limit = in_array($limit, [25, 50, 75, 100, 'all']) ? $limit : 25;
        $page = max(1, (int)$page);
        
        $result = $pokemonRepository->filterAllWithPagination($generation, $type, $search, $limit, $page, $sort, $order);
        $generations = $pokemonRepository->findAllGenerations();
        $types = $typeRepository->findAll();

        return $this->render('pokedex/index.html.twig', [
            'pokemons' => $result['pokemons'],        
            'totalPokemons' => $result['total'],     
            'currentPage' => $page,
            'totalPages' => $result['totalPages'],
            'generations' => $generations,
            'types' => $types,
            'current_generation' => $generation,
            'current_type' => $type,
            'current_search' => $search,
            'current_limit' => $limit,    
            'sort' => $sort,
            'order' => $order,           
        ]);
    }
}
