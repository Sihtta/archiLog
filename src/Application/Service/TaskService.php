<?php

namespace App\Application\Service;

use App\Application\Port\Repository\TaskRepositoryInterface;
use App\Domain\Task\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private TaskRepositoryInterface $taskRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(TaskRepositoryInterface $taskRepository, EntityManagerInterface $entityManager)
    {
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
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
    }
}
