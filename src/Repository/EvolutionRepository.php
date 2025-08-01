<?php

namespace App\Repository;

use App\Entity\Evolution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evolution>
 *
 * @method Evolution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evolution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evolution[]    findAll()
 * @method Evolution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvolutionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evolution::class);
    }

//    /**
//     * @return Evolution[] Returns an array of Evolution objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Evolution
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
