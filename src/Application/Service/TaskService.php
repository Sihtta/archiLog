<?php

namespace App\Application\Service;

use App\Application\Port\Repository\TaskRepositoryInterface;

class TaskService
{
    private TaskRepositoryInterface $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function createTask(string $title): void
    {
    }
}
