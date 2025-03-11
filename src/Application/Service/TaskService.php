<?php

namespace App\Application\Service;

use App\Application\Port\Repository\TaskRepositoryInterface;
use App\Domain\Task\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private TaskRepositoryInterface $taskRepository;
    private EntityManagerInterface $entityManager;
    private NotificationService $notificationService;
    private TaskReportService $taskReportService;
    private array $observers = [];

    public function __construct(
        TaskRepositoryInterface $taskRepository,
        EntityManagerInterface $entityManager,
        NotificationService $notificationService,
        TaskReportService $taskReportService
    ) {
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
        $this->taskReportService = $taskReportService;

        // Ajouter l'observateur NotificationService
        $this->observers[] = $this->notificationService;
    }

    public function createTask(string $title, ?string $description, ?\DateTime $dueDate, $user): Task
    {
        $task = new Task();
        $task->setTitle($title);
        $task->setDescription($description);
        $task->setDueDate($dueDate);
        $task->setUser($user);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    public function updateTask(Task $task): void
    {
        $this->entityManager->flush();
    }

    public function deleteTask(Task $task): void
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    public function getTasksByUser($user): array
    {
        return $this->taskRepository->findByUser($user->getId());
    }

    // Notifie tous les observateurs de l'événement
    private function notifyObservers(string $message): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($message);
        }
    }

    // Vérifie les deadlines et notifie les observateurs
    public function checkTaskDeadlines(): void
    {
        $tasks = $this->taskRepository->findTasksWithUpcomingDeadlines();

        foreach ($tasks as $task) {
            $user = $task->getUser();
            $fullName = $user ? $user->getFullName() : 'Utilisateur inconnu';

            $message = sprintf(
                "⚠️ La tâche **%s** arrive à échéance le %s !\n\nPersonne concernée : %s",
                $task->getTitle(),
                $task->getDueDate()?->format('d/m/Y H:i'),
                $fullName
            );

            // Notifier les observateurs (ici, NotificationService)
            $this->notifyObservers($message);
        }
    }

    public function updateTaskStatus(Task $task, string $status): void
    {
        $task->setStatus($status);

        if ($status === Task::STATUS_DONE) {
            $user = $task->getUser();
            $task->setCompletedAt(new \DateTime());

            if ($user) {
                $user->addExp(20);
                $this->entityManager->persist($user);
            }

            // Générer le rapport des tâches terminées
            $this->taskReportService->generateDailyReport();
        } else {
            $task->setCompletedAt(null);
        }

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $message = sprintf(
            "La tâche **%s** a été mise à jour avec le statut : **%s**",
            $task->getTitle(),
            $status
        );

        // Notifier les observateurs (ici, NotificationService)
        $this->notifyObservers($message);
    }
}
