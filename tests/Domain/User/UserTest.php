<?php

namespace Tests\Domain\User;

use PHPUnit\Framework\TestCase;
use App\Domain\User\Entity\User;
use App\Domain\Task\Entity\Task;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;
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
    
    public function testInvalidEmail()
    {
        $validator = Validation::createValidator();
        $constraint = new Email();
        
        $violations = $validator->validate('invalid-email', $constraint);
        $this->assertGreaterThan(0, count($violations));
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
    
    public function testExpCannotBeNegative()
    {
        $user = new User();
        $user->setExp(-50);
        $this->assertEquals(0, $user->getExp());
    }

    public function testTreeImage()
    {
        $user = new User();
        $user->setExp(350);
        $this->assertEquals('images/4.png', $user->getTreeImage());
    }

    public function testPasswordManagement()
    {
        $user = new User();
        $user->setPassword('hashedpassword');
        $this->assertEquals('hashedpassword', $user->getPassword());
        
        $user->setPlainPassword('plaintext');
        $this->assertEquals('plaintext', $user->getPlainPassword());
        
        $user->eraseCredentials();
        $this->assertNull($user->getPlainPassword());
    }

    public function testFullNameAndPseudo()
    {
        $user = new User();
        $user->setFullName('John Doe');
        $this->assertEquals('John Doe', $user->getFullName());
        
        $user->setPseudo('JD');
        $this->assertEquals('JD', $user->getPseudo());
        
        $this->assertEquals('JD', (string) $user);
        
        $user->setPseudo(null);
        $this->assertEquals('John Doe', (string) $user);
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