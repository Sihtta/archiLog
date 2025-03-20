<?php

namespace Tests\Domain\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);

        // Nettoyage de la table User avant chaque test
        $this->entityManager->createQuery('DELETE FROM App\Domain\User\Entity\User')->execute();
    }

    private function createUser(string $email, int $exp): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setFullName('Test User');
        $user->setExp($exp);
        $user->setPassword('hashed_password');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function testFindOneByEmail(): void
    {
        $user = $this->createUser('test1@example.com', 100);

        $foundUser = $this->userRepository->findOneByEmail('test1@example.com');
        $this->assertNotNull($foundUser);
        $this->assertEquals('test1@example.com', $foundUser->getEmail());
    }

    public function testFindTopUsersByExp(): void
    {
        $this->createUser('test1@example.com', 100);
        $this->createUser('test2@example.com', 200);
        $this->createUser('test3@example.com', 50);

        $users = $this->userRepository->findTopUsersByExp(2);

        $this->assertCount(2, $users);
        $this->assertEquals(200, $users[0]->getExp());
        $this->assertEquals(100, $users[1]->getExp());
    }

    public function testUpgradePassword(): void
    {
        $user = $this->createUser('test1@example.com', 100);

        $newHashedPassword = 'new_hashed_password';
        $this->userRepository->upgradePassword($user, $newHashedPassword);

        $updatedUser = $this->userRepository->findOneByEmail('test1@example.com');
        $this->assertEquals($newHashedPassword, $updatedUser->getPassword());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
