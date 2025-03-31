<?php

namespace App\Application\Port\Repository;

use App\Domain\User\Entity\UsernameHistory;
use App\Domain\User\Repository\UsernameHistoryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour la gestion de l'historique des changements de pseudonyme.
 *
 * @extends ServiceEntityRepository<UsernameHistory>
 */
class UsernameHistoryRepository extends ServiceEntityRepository implements UsernameHistoryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsernameHistory::class);
    }

    /**
     * Recherche un historique de changement de pseudonyme par son identifiant.
     */
    public function find($id, $lockMode = null, $lockVersion = null): ?UsernameHistory
    {
        return parent::find($id, $lockMode, $lockVersion);
    }

    /**
     * Recherche un historique selon des critères donnés.
     */
    public function findOneBy(array $criteria, array $orderBy = null): ?UsernameHistory
    {
        return parent::findOneBy($criteria, $orderBy);
    }

    /**
     * Récupère tous les historiques de changement de pseudonyme.
     */
    public function findAll(): array
    {
        return parent::findAll();
    }

    /**
     * Recherche plusieurs historiques selon des critères donnés.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
    }

    //    /**
    //     * @return UsernameHistory[] Returns an array of UsernameHistory objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult();
    //    }

    //    public function findOneBySomeField($value): ?UsernameHistory
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult();
    //    }
}