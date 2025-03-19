<?php

namespace Tests\Domain\Task;

use App\Domain\Task\Repository\TaskRepository;
use App\Domain\Task\Entity\Task;
use App\Domain\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;

    protected function setUp(): void
{
    self::bootKernel();
    $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
    $this->taskRepository = self::getContainer()->get(TaskRepository::class);

    // Nettoyage des tables
    $this->entityManager->createQuery('DELETE FROM App\Domain\Task\Entity\Task')->execute();
    $this->entityManager->createQuery('DELETE FROM App\Domain\User\Entity\User')->execute();

    // Création d'un utilisateur de test
    $user = new User();
    $user->setEmail('test@example.com');
    $user->setFullName('Test User');
    $user->setExp(100);
    $user->setPassword('hashed_password');
    
    $this->entityManager->persist($user);
    $this->entityManager->flush();

    // Création de tâches avec un utilisateur
    $task1 = (new Task())
        ->setTitle('Task 1')
        ->setStatus(Task::STATUS_TODO)
        ->setUser($user);  // 🔥 Associer l'utilisateur

    $task2 = (new Task())
        ->setTitle('Task 2')
        ->setStatus(Task::STATUS_IN_PROGRESS)
        ->setUser($user);  // 🔥 Associer l'utilisateur

    $this->entityManager->persist($task1);
    $this->entityManager->persist($task2);
    $this->entityManager->flush();
}

public function testSaveTask(): void
{
    // Récupération de l'utilisateur créé dans setUp()
    $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);

    $task = new Task();
    $task->setTitle('Test Task');
    $task->setStatus(Task::STATUS_DONE);
    $task->setUser($user);  // 🔥 Associer l'utilisateur

    $this->taskRepository->save($task);
    $this->entityManager->clear();

    $retrievedTask = $this->taskRepository->findOneBy(['title' => 'Test Task']);
    $this->assertNotNull($retrievedTask);
    $this->assertEquals(Task::STATUS_DONE, $retrievedTask->getStatus());
}

    public function testFindByStatus(): void
    {
        $tasks = $this->taskRepository->findByStatus(Task::STATUS_TODO);
        $this->assertCount(1, $tasks);
        $this->assertEquals('Task 1', $tasks[0]->getTitle());
    }

    public function testDeleteTask(): void
    {
        $task = $this->taskRepository->findOneBy(['title' => 'Task 1']);
        $this->assertNotNull($task);

        $this->taskRepository->delete($task);
        $this->entityManager->clear();

        $retrievedTask = $this->taskRepository->findOneBy(['title' => 'Task 1']);
        $this->assertNull($retrievedTask);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
