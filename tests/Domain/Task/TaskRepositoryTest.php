<?php

namespace Tests\Domain\Task;

use App\Domain\Task\Repository\TaskRepository;
use App\Domain\Task\Entity\Task;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\DriverManager;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;

class TaskRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;

    protected function setUp(): void
{
    $config = ORMSetup::createConfiguration(true);
    $config->setMetadataDriverImpl(new AttributeDriver([__DIR__ . '/../../../src/Domain/Task/Entity']));

    $connectionParams = ['url' => 'sqlite:///:memory:'];
    $connection = DriverManager::getConnection($connectionParams, $config);
    $this->entityManager = new EntityManager($connection, $config);

    // Mock de ManagerRegistry
    /** @var ManagerRegistry&MockObject $registryMock */
    $registryMock = $this->createMock(ManagerRegistry::class);
    $registryMock->method('getManagerForClass')->willReturn($this->entityManager);

    // Correction : on passe maintenant le registryMock
    $this->taskRepository = new TaskRepository($registryMock);
}


    public function testSaveTask(): void
    {
        $task = new Task();
        $task->setTitle('Test Task');
        $task->setStatus('pending');

        $this->taskRepository->save($task);
        $this->entityManager->clear();

        $retrievedTask = $this->taskRepository->findOneBy(['title' => 'Test Task']);
        $this->assertNotNull($retrievedTask);
        $this->assertEquals('pending', $retrievedTask->getStatus());
    }

    public function testFindByStatus(): void
    {
        $task1 = new Task();
        $task1->setTitle('Task 1');
        $task1->setStatus('completed');
        $this->taskRepository->save($task1);

        $task2 = new Task();
        $task2->setTitle('Task 2');
        $task2->setStatus('completed');
        $this->taskRepository->save($task2);

        $this->entityManager->clear();

        $tasks = $this->taskRepository->findByStatus('completed');
        $this->assertCount(2, $tasks);
    }

    public function testDeleteTask(): void
    {
        $task = new Task();
        $task->setTitle('To be deleted');
        $this->taskRepository->save($task);

        $this->taskRepository->delete($task);
        $this->entityManager->clear();

        $retrievedTask = $this->taskRepository->findOneBy(['title' => 'To be deleted']);
        $this->assertNull($retrievedTask);
    }
}
