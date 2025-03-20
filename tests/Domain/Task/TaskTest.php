<?php

namespace Tests\Domain\Task;

use App\Domain\Task\Entity\Task;
use App\Domain\User\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTaskInitialization(): void
    {
        $task = new Task();

        $this->assertNotNull($task->getCreatedAt());
        $this->assertSame(Task::STATUS_TODO, $task->getStatus());
    }

    public function testSetTitle(): void
    {
        $task = new Task();
        $task->setTitle('New Task');

        $this->assertSame('New Task', $task->getTitle());
    }

    public function testSetDescription(): void
    {
        $task = new Task();
        $task->setDescription('This is a test task.');

        $this->assertSame('This is a test task.', $task->getDescription());
    }

    public function testSetDueDate(): void
    {
        $task = new Task();
        $dueDate = new \DateTime('2025-01-01');
        $task->setDueDate($dueDate);

        $this->assertSame($dueDate, $task->getDueDate());
    }

    public function testSetStatus(): void
    {
        $task = new Task();
        $task->setStatus(Task::STATUS_IN_PROGRESS);

        $this->assertSame(Task::STATUS_IN_PROGRESS, $task->getStatus());
    }

    public function testSetUser(): void
    {
        $task = new Task();
        $user = $this->createMock(User::class);
        $task->setUser($user);

        $this->assertSame($user, $task->getUser());
    }

    public function testIsDone(): void
    {
        $task = new Task();
        $task->setStatus(Task::STATUS_DONE);

        $this->assertTrue($task->isDone());
    }

    public function testSetCompletedAt(): void
    {
        $task = new Task();
        $completedAt = new \DateTime();
        $task->setCompletedAt($completedAt);

        $this->assertSame($completedAt, $task->getCompletedAt());
    }

    public function testNewTaskHasDefaultValues(): void
    {
        $task = new Task();

        $this->assertNull($task->getId());
        $this->assertNull($task->getTitle());
        $this->assertNull($task->getDescription());
        $this->assertNull($task->getDueDate());
        $this->assertSame(Task::STATUS_TODO, $task->getStatus());
        $this->assertNull($task->getUser());
        $this->assertNull($task->getCompletedAt());
    }

    public function testTaskCanBeMarkedAsCompleted(): void
    {
        $task = new Task();
        $task->setStatus(Task::STATUS_DONE);
        $completedAt = new \DateTime();
        $task->setCompletedAt($completedAt);

        $this->assertTrue($task->isDone());
        $this->assertSame($completedAt, $task->getCompletedAt());
    }

    public function testDueDateCanBeNull(): void
    {
        $task = new Task();
        $task->setDueDate(null);

        $this->assertNull($task->getDueDate());
    }

    public function testCompletedAtCanBeNull(): void
    {
        $task = new Task();
        $task->setCompletedAt(null);

        $this->assertNull($task->getCompletedAt());
    }
}
