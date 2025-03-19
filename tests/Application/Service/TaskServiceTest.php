<?php

namespace Tests\Application\Service;

use App\Application\Service\TaskService;
use App\Application\Port\Repository\TaskRepositoryInterface;
use App\Application\Service\NotificationService;
use App\Application\Service\TaskReportService;
use App\Domain\Task\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TaskServiceTest extends TestCase
{
    private TaskService $taskService;
    private TaskRepositoryInterface $taskRepository;
    private EntityManagerInterface $entityManager;
    private NotificationService $notificationService;
    private TaskReportService $taskReportService;

    protected function setUp(): void
    {
        $this->taskRepository = $this->createMock(TaskRepositoryInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->notificationService = $this->createMock(NotificationService::class);
        $this->taskReportService = $this->createMock(TaskReportService::class);

        $this->taskService = new TaskService(
            $this->taskRepository,
            $this->entityManager,
            $this->notificationService,
            $this->taskReportService
        );
    }

    public function testCreateTaskPersistsAndReturnsTask()
    {
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $task = $this->taskService->createTask('Test Task', 'Test Description', null, 'pending', null);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Test Task', $task->getTitle());
    }

    public function testUpdateTaskCallsFlush()
    {
        $task = $this->createMock(Task::class);
        $this->entityManager->expects($this->once())->method('flush');
        
        $this->taskService->updateTask($task);
    }

    public function testDeleteTaskCallsRemoveAndFlush()
    {
        $task = $this->createMock(Task::class);
        $this->entityManager->expects($this->once())->method('remove')->with($task);
        $this->entityManager->expects($this->once())->method('flush');
        
        $this->taskService->deleteTask($task);
    }

    public function testUpdateTaskStatusNotifiesObservers()
    {
        $task = $this->createMock(Task::class);
        $task->method('getTitle')->willReturn('Test Task');
        
        $this->notificationService->expects($this->once())->method('update');
        $this->taskReportService->expects($this->never())->method('logCompletedTask');

        $this->taskService->updateTaskStatus($task, 'in_progress');
    }

    public function testUpdateTaskStatusLogsCompletedTask()
    {
        $task = $this->createMock(Task::class);
        $task->method('getTitle')->willReturn('Test Task');
        $task->method('getUser')->willReturn(null);

        $this->notificationService->expects($this->once())->method('update');
        $this->taskReportService->expects($this->once())->method('logCompletedTask')->with($task);
        
        $this->taskService->updateTaskStatus($task, Task::STATUS_DONE);
    }
}