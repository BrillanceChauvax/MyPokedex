<?php

namespace App\Repository;

use App\Entity\Pokemon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pokemon>
 *
 * @method Pokemon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pokemon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pokemon[]    findAll()
 * @method Pokemon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PokemonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    public function filterAll($generation, $type, $search): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.types', 'pt')
            ->leftJoin('pt.type', 't')
            ->leftJoin('p.evolutionsDepuis', 'ev')
            ->leftJoin('ev.pokemonFin', 'pf')
            ->addSelect('pt', 't', 'ev', 'pf');

        if ($generation) {
            $qb->andWhere('p.generation = :gen')->setParameter('gen', $generation);
        }

        if ($type) {
            $qb->andWhere('t.id = :type')->setParameter('type', $type);
        }

        if ($search) {
            $qb
                ->andWhere('p.nom LIKE :search OR p.numero_national = :num')
                ->setParameter('search', '%' . $search . '%')
                ->setParameter('num', is_numeric($search) ? (int)$search : -1);
        }

        $qb->orderBy('p.numero_national', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findAllGenerations(): array
    {
        return $this->createQueryBuilder('p')
            ->select('DISTINCT p.generation')
            ->orderBy('p.generation')
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function filterAllWithPagination($generation, $type, $search, $limit, $page, $sort = 'numero', $order = 'asc'): array
    {
        $qbIds = $this->createQueryBuilder('p')
            ->select('DISTINCT p.id');
        
        if ($generation) {
            $qbIds->andWhere('p.generation = :gen')->setParameter('gen', $generation);
        }
        
        if ($type) {
            $qbIds->andWhere('EXISTS (
                SELECT 1 FROM App\Entity\PokemonType pt2 
                JOIN App\Entity\Type t2 WITH pt2.type = t2.id 
                WHERE pt2.pokemon = p.id AND t2.id = :type
            )')->setParameter('type', $type);
        }
        
        if ($search) {
            $qbIds->andWhere('p.nom LIKE :search OR p.numero_national = :num')
                  ->setParameter('search', '%' . $search . '%')
                  ->setParameter('num', is_numeric($search) ? (int)$search : -1);
        }
        
        switch($sort) {
            case 'generation':
                $qbIds->orderBy('p.generation', $order);
                break;
            case 'numero':
                $qbIds->orderBy('p.numero_national', $order);
                break;
            case 'nom':
                $qbIds->orderBy('p.nom', $order);
                break;
            default:
                $qbIds->orderBy('p.numero_national', $order);
        }
        
        $countQb = clone $qbIds;
        $countQb->select('COUNT(DISTINCT p.id)');
        $total = $countQb->getQuery()->getSingleScalarResult();
        
        if ($limit !== 'all') {
            $offset = ((int)$limit) * ($page - 1);
            $qbIds->setFirstResult($offset)->setMaxResults((int)$limit);
            $totalPages = ceil($total / (int)$limit);
        } else {
            $totalPages = 1;
        }
        
        $pokemonIds = array_column($qbIds->getQuery()->getScalarResult(), 'id');
        
        if (empty($pokemonIds)) {
            return [
                'pokemons' => [],
                'total' => $total,
                'totalPages' => $totalPages
            ];
        }
        
        $qbComplete = $this->createQueryBuilder('p')
            ->leftJoin('p.types', 'pt')
            ->leftJoin('pt.type', 't')
            ->leftJoin('p.evolutionsDepuis', 'ev')
            ->leftJoin('ev.pokemonFin', 'pf')
            ->addSelect('pt', 't', 'ev', 'pf')
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $pokemonIds);
        
        switch($sort) {
            case 'generation':
                $qbComplete->orderBy('p.generation', $order);
                break;
            case 'numero':
                $qbComplete->orderBy('p.numero_national', $order);
                break;
            case 'nom':
                $qbComplete->orderBy('p.nom', $order);
                break;
            default:
                $qbComplete->orderBy('p.numero_national', $order);
        }
        
        $pokemons = $qbComplete->getQuery()->getResult();
        
        return [
            'pokemons' => $pokemons,
            'total' => $total,
            'totalPages' => $totalPages
        ];
    }

    //    /**
    //     * @return Pokemon[] Returns an array of Pokemon objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Pokemon
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
