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

class TaskRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        // Configuration ORM pour Doctrine 3.3
        $config = ORMSetup::createConfiguration(true);
        $config->setMetadataDriverImpl(new AttributeDriver([__DIR__ . '/../../../src/Domain/Task/Entity']));

        $connection = ['driver' => 'pdo_sqlite', 'memory' => true];

        $connectionParams = ['url' => 'sqlite:///:memory:'];
        $connection = DriverManager::getConnection($connectionParams, $config);
        $this->entityManager = new EntityManager($connection, $config);

        // Création du schéma en mémoire
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->updateSchema($metadata);

        $this->taskRepository = new TaskRepository($this->entityManager);
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
