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

        // Nettoyage des tables avant chaque test
        $this->entityManager->createQuery('DELETE FROM App\Domain\Task\Entity\Task')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Domain\User\Entity\User')->execute();
    }

    private function createUser(): User
    {
        $user = new User();
        $user->setEmail('test_' . uniqid() . '@example.com');
        $user->setFullName('Test User');
        $user->setExp(100);
        $user->setPassword('hashed_password');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function testSaveTask(): void
    {
        $user = $this->createUser();

        $task = new Task();
        $task->setTitle('Test Task');
        $task->setStatus(Task::STATUS_DONE);
        $task->setUser($user);

        $this->taskRepository->save($task);
        $this->entityManager->clear();

        $retrievedTask = $this->taskRepository->findOneBy(['title' => 'Test Task']);
        $this->assertNotNull($retrievedTask);
        $this->assertEquals(Task::STATUS_DONE, $retrievedTask->getStatus());
    }

    public function testFindByStatus(): void
    {
        $user = $this->createUser();

        $task1 = (new Task())->setTitle('Task 1')->setStatus(Task::STATUS_TODO)->setUser($user);
        $task2 = (new Task())->setTitle('Task 2')->setStatus(Task::STATUS_IN_PROGRESS)->setUser($user);

        $this->entityManager->persist($task1);
        $this->entityManager->persist($task2);
        $this->entityManager->flush();

        $tasks = $this->taskRepository->findByStatus(Task::STATUS_TODO);
        $this->assertCount(1, $tasks);
        $this->assertEquals('Task 1', $tasks[0]->getTitle());
    }

    public function testDeleteTask(): void
    {
        $user = $this->createUser();

        $task = new Task();
        $task->setTitle('Task to Delete');
        $task->setStatus(Task::STATUS_TODO);
        $task->setUser($user);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $retrievedTask = $this->taskRepository->findOneBy(['title' => 'Task to Delete']);
        $this->assertNotNull($retrievedTask);

        $this->taskRepository->delete($retrievedTask);
        $this->entityManager->clear();

        $deletedTask = $this->taskRepository->findOneBy(['title' => 'Task to Delete']);
        $this->assertNull($deletedTask);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
