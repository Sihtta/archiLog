<?php

namespace Tests\Domain\User;

use App\Domain\User\Entity\UsernameHistory;
use App\Domain\User\Repository\UsernameHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class UsernameHistoryRepositoryTest extends TestCase
{
    private UsernameHistoryRepository $usernameHistoryRepository;

    protected function setUp(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->usernameHistoryRepository = new UsernameHistoryRepository($managerRegistry);
    }

    public function testFindAll(): void
    {
        $history = $this->usernameHistoryRepository->findAll();
        $this->assertIsArray($history);
    }

    public function testFindOneBy(): void
    {
        $history = $this->usernameHistoryRepository->findOneBy(['id' => 1]);
        $this->assertNull($history); // Par défaut, il n'y a pas de données en base
    }
}
