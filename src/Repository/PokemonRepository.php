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
    public function filterAllWithPagination($generation, $type, $search, $limit, $page): array
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
        
        // Compter le total sans limitation
        $countQb = clone $qb;
        $total = $countQb->select('COUNT(DISTINCT p.id)')->getQuery()->getSingleScalarResult();
        
        // Si limit n'est pas "all", appliquer la pagination
        if ($limit !== 'all') {
            $offset = ((int)$limit) * ($page - 1);
            $qb->setFirstResult($offset)->setMaxResults((int)$limit);
            $totalPages = ceil($total / (int)$limit);
        } else {
            $totalPages = 1;
        }

        $pokemons = $qb->getQuery()->getResult();

        return [
            'pokemons' => $pokemons,
            'total' => $total,
            'totalPages' => $totalPages
        ];
    }
}
