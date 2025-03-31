<?php

namespace App\Application\Port\Repository;

use App\Domain\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository implements TaskRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * Récupère toutes les tâches d'un utilisateur spécifique, triées par date de création décroissante.
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->setParameter('user', $userId)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère toutes les tâches avec un statut donné, triées par date de création décroissante.
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', $status)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Sauvegarde une tâche en base de données.
     */
    public function save(Task $task): void
    {
        $em = $this->getEntityManager();
        $em->persist($task);
        $em->flush();
    }

    /**
     * Supprime une tâche de la base de données.
     */
    public function delete(Task $task): void
    {
        $em = $this->getEntityManager();
        $em->remove($task);
        $em->flush();
    }

    /**
     * Récupère les tâches ayant une date limite imminente (dans les 24 heures).
     */
    public function findTasksWithUpcomingDeadlines(): array
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.dueDate IS NOT NULL')
            ->andWhere('t.dueDate BETWEEN :now AND :soon')
            ->setParameter('now', new \DateTime())
            ->setParameter('soon', (new \DateTime())->modify('+1 day'))
            ->getQuery();

        return $qb->getResult();
    }
}