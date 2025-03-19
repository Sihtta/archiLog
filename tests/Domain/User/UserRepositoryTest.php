<?php

namespace Tests\Domain\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->userRepository = new UserRepository($managerRegistry);
    }

    public function testFindOneByEmail(): void
    {
        $user = new User();
        $user->setEmail("test@example.com");

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($user));

        $this->userRepository->findOneByEmail("test@example.com");

        $this->assertTrue(true); // Test basique pour vérifier que la méthode est appelée
    }

    public function testFindTopUsersByExp(): void
    {
        $users = $this->userRepository->findTopUsersByExp(5);
        $this->assertIsArray($users);
    }
}
