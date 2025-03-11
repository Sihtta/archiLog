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
        $this->reportDirectory = rtrim($reportDirectory, '/');
        $this->entityManager = $entityManager;
    }

    public function logCompletedTask(Task $task): void
    {
        $date = (new \DateTime())->format('Y-m-d');
        $filePath = "{$this->reportDirectory}/tasks_completed_{$date}.txt";

        // Vérifier si la tâche est déjà enregistrée
        if (file_exists($filePath)) {
            $existingContent = file_get_contents($filePath);
            if (str_contains($existingContent, "ID: {$task->getId()}")) {
                return; // La tâche est déjà enregistrée, on ne l'ajoute pas à nouveau
            }
        }

        // Récupérer les infos supplémentaires
        $description = $task->getDescription() ?: "Aucune description";
        $dueDate = $task->getDueDate()?->format('d/m/Y H:i') ?: "Pas de date limite";
        $userEmail = $task->getUser()?->getEmail() ?? "Email inconnu";

        // Ajouter la nouvelle tâche terminée avec les détails supplémentaires
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
