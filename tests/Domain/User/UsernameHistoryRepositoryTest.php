<?php
namespace Tests\Domain\User;

use App\Domain\User\Entity\UsernameHistory;
use App\Domain\User\Repository\UsernameHistoryRepository;
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

    // Nettoyage
    $this->entityManager->createQuery('DELETE FROM App\Domain\User\Entity\UsernameHistory')->execute();
    $this->entityManager->createQuery('DELETE FROM App\Domain\User\Entity\User')->execute();

    // Création d'un utilisateur test
    $user = new \App\Domain\User\Entity\User();
    $user->setEmail('test@example.com')
        ->setFullName('Test User')
        ->setPseudo('testUser')
        ->setPassword('hashed_password'); // Mets un hash fictif

    $this->entityManager->persist($user);
    $this->entityManager->flush(); // On sauvegarde l'utilisateur pour avoir son ID

    // Création d'un historique avec un utilisateur
    $history1 = (new UsernameHistory())
        ->setUser($user) // 🔥 Ajout de l'utilisateur ici
        ->setOldPseudo('oldUser1')
        ->setNewPseudo('newUser1')
        ->setChangedAt(new \DateTimeImmutable());

    $this->entityManager->persist($history1);
    $this->entityManager->flush();
}

    public function testFindAll(): void
    {
        $history = $this->usernameHistoryRepository->findAll();
        $this->assertIsArray($history);
        $this->assertCount(1, $history);
    }

    public function testFindOneBy(): void
    {
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
