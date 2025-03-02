<?php

namespace App\Application\Service;

use App\Application\Port\Repository\TaskRepositoryInterface;

class TaskService
{
    private TaskRepositoryInterface $taskRepository;

    // Constructeur avec injection de dépendances
    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    // Méthodes du service, par exemple :
    public function createTask(string $title): void
    {
        // Implémentation de la logique
    }
}
