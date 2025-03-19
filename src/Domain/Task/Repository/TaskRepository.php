<?php

namespace App\Domain\Task\Repository;

use App\Application\Port\Repository\TaskRepositoryInterface;
use App\Domain\Task\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository implements TaskRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->setParameter('user', $userId)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', $status)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function save(Task $task): void
    {
        $this->_em->persist($task);
        $this->_em->flush();
    }

    public function delete(Task $task): void
    {
        $this->_em->remove($task);
        $this->_em->flush();
    }

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
