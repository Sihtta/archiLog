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
        $this->reportDirectory = $reportDirectory;
        $this->entityManager = $entityManager;
    }

    public function generateDailyReport(): void
    {
        $today = (new \DateTime())->format('Y-m-d');
        $filePath = $this->reportDirectory . "/tasks_completed_{$today}.csv";

        // Récupérer les tâches terminées aujourd'hui
        $tasks = $this->entityManager->getRepository(Task::class)
            ->createQueryBuilder('t')
            ->where('t.completedAt IS NOT NULL')
            ->andWhere('t.completedAt >= :startOfDay')
            ->setParameter('startOfDay', new \DateTime('today'))
            ->orderBy('t.completedAt', 'ASC')
            ->getQuery()
            ->getResult();

        if (empty($tasks)) {
            return; // Rien à enregistrer
        }

        // Création du fichier s'il n'existe pas
        $handle = fopen($filePath, 'w');

        // Ajouter les en-têtes CSV
        fputcsv($handle, ['ID', 'Titre', 'Description', 'Utilisateur', 'Date de complétion']);

        // Ajouter les tâches au fichier
        foreach ($tasks as $task) {
            fputcsv($handle, [
                $task->getId(),
                $task->getTitle(),
                $task->getDescription(),
                $task->getUser()?->getEmail(), // Ou un autre identifiant utilisateur
                $task->getCompletedAt()->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($handle);
    }
}
