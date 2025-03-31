<?php

namespace App\Application\Service;

use App\Domain\Task\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

class TaskReportService
{
    private string $reportDirectory;
    private EntityManagerInterface $entityManager;

    public function __construct(string $reportDirectory, EntityManagerInterface $entityManager)
    {
        $this->reportDirectory = rtrim($reportDirectory, '/'); // S'assure que le chemin ne se termine pas par un "/"
        $this->entityManager = $entityManager;
    }

    /**
     * Enregistre une tâche complétée dans un fichier de rapport quotidien.
     */
    public function logCompletedTask(Task $task): void
    {
        $date = (new \DateTime())->format('Y-m-d');
        $filePath = "{$this->reportDirectory}/tasks_completed_{$date}.txt";

        // Vérifie si la tâche est déjà enregistrée pour éviter les doublons
        if (file_exists($filePath)) {
            $existingContent = file_get_contents($filePath);
            if (str_contains($existingContent, "ID: {$task->getId()}")) {
                return;
            }
        }

        $description = $task->getDescription() ?: "Aucune description";
        $dueDate = $task->getDueDate()?->format('d/m/Y H:i') ?: "Pas de date limite";
        $userEmail = $task->getUser()?->getEmail() ?? "Email inconnu";

        $newEntry = sprintf(
            "[%s] ID: %d | Tâche: %s | Description: %s | Date limite: %s | Utilisateur: %s (%s)\n",
            $task->getCompletedAt()?->format('H:i:s'),
            $task->getId(),
            $task->getTitle(),
            $description,
            $dueDate,
            $task->getUser()?->getFullName() ?? 'Inconnu',
            $userEmail
        );

        file_put_contents($filePath, $newEntry, FILE_APPEND);
    }
}