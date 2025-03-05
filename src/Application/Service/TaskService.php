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

    public function createTask(string $title, $user): Task
    {
        $task = new Task();
        $task->setTitle($title);
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

    public function getAllTasks(): array
    {
        return $this->taskRepository->findBy([], ['createdAt' => 'DESC']);
    }
}
