<?php

namespace App\Application\Port\Repository;

use App\Domain\Task\Entity\Task;

interface TaskRepositoryInterface
{
    public function findByStatus(string $status): array;

    public function findByUser(int $userId): array;

    public function save(Task $task): void;

    public function delete(Task $task): void;
}
