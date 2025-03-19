<?php

namespace Tests\Application\Service;

use App\Application\Service\TaskReportService;
use App\Domain\Task\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TaskReportServiceTest extends TestCase
{
    private string $reportDirectory;
    private TaskReportService $taskReportService;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->reportDirectory = sys_get_temp_dir() . '/task_reports';
        if (!is_dir($this->reportDirectory)) {
            mkdir($this->reportDirectory);
        }

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->taskReportService = new TaskReportService($this->reportDirectory, $this->entityManager);
    }

    public function testLogCompletedTaskCreatesReportFile()
    {
        $task = $this->createMock(Task::class);
        $task->method('getId')->willReturn(1);
        $task->method('getTitle')->willReturn('Test Task');
        $task->method('getDescription')->willReturn('Test Description');
        $task->method('getDueDate')->willReturn(null);
        $task->method('getUser')->willReturn(null);
        $task->method('getCompletedAt')->willReturn(new \DateTime());
        
        $this->taskReportService->logCompletedTask($task);

        $date = (new \DateTime())->format('Y-m-d');
        $filePath = "{$this->reportDirectory}/tasks_completed_{$date}.txt";

        $this->assertFileExists($filePath);
        $this->assertStringContainsString('Test Task', file_get_contents($filePath));
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob("{$this->reportDirectory}/*"));
        rmdir($this->reportDirectory);
    }
}