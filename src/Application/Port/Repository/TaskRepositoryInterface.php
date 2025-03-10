<?php

namespace App\Application\Port\Repository;

use App\Domain\Task\Entity\Task;
use Doctrine\Persistence\ObjectRepository;

interface TaskRepositoryInterface extends ObjectRepository
{
    public function findByStatus(string $status): array;

    public function findByUser(int $userId): array;

    public function save(Task $task): void;

    public function delete(Task $task): void;

    public function findTasksWithUpcomingDeadlines(): array;
}
