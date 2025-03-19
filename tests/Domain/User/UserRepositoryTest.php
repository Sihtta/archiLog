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

        // Nettoyer la base et insérer des données de test
        $this->entityManager->createQuery('DELETE FROM App\Domain\User\Entity\User')->execute();

        $user1 = (new User())
            ->setEmail('test1@example.com')
            ->setFullName('User One') // Ajout de fullName
            ->setExp(100)
            ->setPassword('hashed_password1');

        $user2 = (new User())
            ->setEmail('test2@example.com')
            ->setFullName('User Two') // Ajout de fullName
            ->setExp(200)
            ->setPassword('hashed_password2');

        $user3 = (new User())
            ->setEmail('test3@example.com')
            ->setFullName('User Three') // Ajout de fullName
            ->setExp(50)
            ->setPassword('hashed_password3');

        $this->entityManager->persist($user1);
        $this->entityManager->persist($user2);
        $this->entityManager->persist($user3);
        $this->entityManager->flush();
    }

    public function testFindOneByEmail(): void
    {
        $user = $this->userRepository->findOneByEmail('test1@example.com');
        $this->assertNotNull($user);
        $this->assertEquals('test1@example.com', $user->getEmail());
    }

    public function testFindTopUsersByExp(): void
    {
        $users = $this->userRepository->findTopUsersByExp(2);

        $this->assertCount(2, $users);
        $this->assertEquals(200, $users[0]->getExp()); // Le plus expérimenté en premier
        $this->assertEquals(100, $users[1]->getExp());
    }

    public function testUpgradePassword(): void
    {
        $user = $this->userRepository->findOneByEmail('test1@example.com');
        $this->assertNotNull($user);

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
