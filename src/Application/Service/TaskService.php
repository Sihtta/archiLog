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

    public function __construct(
        TaskRepositoryInterface $taskRepository,
        EntityManagerInterface $entityManager,
        NotificationService $notificationService
    ) {
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
        $this->notificationService = $notificationService;
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

            $this->notificationService->sendTaskStatusUpdate($message);
        }
    }

    public function updateTaskStatus(Task $task, string $status): void
    {
        $task->setStatus($status);

        if ($status === Task::STATUS_DONE) {
            $user = $task->getUser();
            if ($user) {
                $user->addExp(20);
                $this->entityManager->persist($user);
            }
        }

        $this->entityManager->flush();

        $message = sprintf(
            "La tâche **%s** a été mise à jour avec le statut : **%s**",
            $task->getTitle(),
            $status
        );

        $this->notificationService->sendTaskStatusUpdate($message);
    }
}
