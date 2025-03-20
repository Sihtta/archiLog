<?php

namespace Tests\Domain\User;

use App\Domain\User\Entity\UsernameHistory;
use App\Domain\User\Repository\UsernameHistoryRepository;
use App\Domain\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UsernameHistoryRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private UsernameHistoryRepository $usernameHistoryRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->usernameHistoryRepository = $this->entityManager->getRepository(UsernameHistory::class);

        $this->entityManager->createQuery('DELETE FROM App\Domain\User\Entity\UsernameHistory')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Domain\User\Entity\User')->execute();
    }

    private function createUser(): User
    {
        $user = new User();
        $user->setEmail('test_' . uniqid() . '@example.com')
            ->setFullName('Test User')
            ->setPseudo('testUser')
            ->setPassword('hashed_password');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function createUsernameHistory(User $user, string $oldPseudo, string $newPseudo): UsernameHistory
    {
        $history = new UsernameHistory();
        $history->setUser($user)
                ->setOldPseudo($oldPseudo)
                ->setNewPseudo($newPseudo)
                ->setChangedAt(new \DateTimeImmutable());

        $this->entityManager->persist($history);
        $this->entityManager->flush();

        return $history;
    }

    public function testFindAll(): void
    {
        $user = $this->createUser();
        $this->createUsernameHistory($user, 'oldUser1', 'newUser1');

        $history = $this->usernameHistoryRepository->findAll();
        $this->assertIsArray($history);
        $this->assertCount(1, $history);
    }

    public function testFindOneBy(): void
    {
        $user = $this->createUser();
        $this->createUsernameHistory($user, 'oldUser1', 'newUser1');

        $history = $this->usernameHistoryRepository->findOneBy(['oldPseudo' => 'oldUser1']);
        $this->assertNotNull($history);
        $this->assertEquals('newUser1', $history->getNewPseudo());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
