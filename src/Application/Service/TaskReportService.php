<?php

namespace App\Application\Service;

use App\Domain\Task\Entity\Task;
use Symfony\Component\Filesystem\Filesystem;

class TaskReportService
{
    private string $reportDirectory;
    private Filesystem $filesystem;

    public function __construct(string $reportDirectory)
    {
        $this->reportDirectory = $reportDirectory;
        $this->filesystem = new Filesystem();
    }

    public function logCompletedTask(Task $task): void
    {
        // Vérifier si la tâche est terminée
        if (!$task->getCompletedAt()) {
            return; // Ne rien faire si la tâche n'est pas terminée
        }

        // Générer le nom du fichier du jour
        $date = new \DateTime();
        $fileName = $date->format('Y-m-d') . '.csv';
        $filePath = $this->reportDirectory . DIRECTORY_SEPARATOR . $fileName;

        // Créer le fichier si nécessaire
        if (!$this->filesystem->exists($filePath)) {
            $this->filesystem->touch($filePath);
            // Ajouter l'en-tête CSV si le fichier est créé
            file_put_contents($filePath, "Title,Description,Completion Time\n", FILE_APPEND);
        }

        // Enregistrer les données de la tâche terminée dans le fichier
        $line = sprintf(
            "%s,%s,%s\n",
            $task->getTitle(),
            $task->getDescription(),
            $task->getCompletedAt()->format('Y-m-d H:i:s')
        );
        file_put_contents($filePath, $line, FILE_APPEND);
    }
}
