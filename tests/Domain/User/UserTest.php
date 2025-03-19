<?php

namespace Tests\Domain\User;

use PHPUnit\Framework\TestCase;
use App\Domain\User\Entity\User;
use App\Domain\Task\Entity\Task;
use DateTimeImmutable;

class UserTest extends TestCase
{
    public function testUserCreation()
    {
        $user = new User();
        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->getCreatedAt());
    }

    public function testEmail()
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $this->assertEquals('test@example.com', $user->getEmail());
    }

    public function testRoles()
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testExpManagement()
    {
        $user = new User();
        $user->setExp(100);
        $this->assertEquals(100, $user->getExp());

        $user->addExp(50);
        $this->assertEquals(150, $user->getExp());
    }

    public function testTasksAssociation()
    {
        $user = new User();
        $task = $this->createMock(Task::class);
        $user->addTask($task);
        $this->assertCount(1, $user->getTasks());

        $user->removeTask($task);
        $this->assertCount(0, $user->getTasks());
    }
}
