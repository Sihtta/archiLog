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

        // Nettoyage et insertion de données de test
        $this->entityManager->createQuery('DELETE FROM App\Domain\User\Entity\UsernameHistory')->execute();

        $history1 = (new UsernameHistory())
            ->setOldUsername('oldUser1')
            ->setNewUsername('newUser1')
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
        $history = $this->usernameHistoryRepository->findOneBy(['oldUsername' => 'oldUser1']);
        $this->assertNotNull($history);
        $this->assertEquals('newUser1', $history->getNewUsername());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
