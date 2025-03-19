<?php

namespace Tests\Application\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Domain\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $userPasswordHasher;

    protected function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->userPasswordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        
        // Nettoyer et charger les données de test
        $this->entityManager->getConnection()->executeQuery('DELETE FROM user');
    }

    public function testEditUserAccessDenied()
    {
        $user = $this->createUser('testUser');
        $this->client->request('GET', '/utilisateur/edition/' . $user->getId());
        $this->assertResponseRedirects('/access-denied');
    }

    public function testEditUserSuccessful()
    {
        $user = $this->createUser('oldPseudo');

        $this->client->loginUser($user);
        $this->client->request('GET', '/utilisateur/edition/' . $user->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $this->client->submitForm('Modifier', [
            'user[pseudo]' => 'newPseudo'
        ]);

        $this->assertResponseRedirects('/utilisateur/edition/' . $user->getId());

        $updatedUser = $this->entityManager->getRepository(User::class)->find($user->getId());
        $this->assertEquals('newPseudo', $updatedUser->getPseudo());
    }

    public function testEditPasswordSuccessful()
    {
        $user = $this->createUser('testUser', 'oldPassword');

        $this->client->loginUser($user);
        $this->client->request('GET', '/utilisateur/edition-mot-de-passe/' . $user->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $this->client->submitForm('Modifier mon mot de passe', [
            'user_password[plainPassword][first]' => 'oldPassword',
            'user_password[plainPassword][second]' => 'oldPassword',
            'user_password[newPassword]' => 'newSecurePassword',
        ]);

        $this->assertResponseRedirects('/utilisateur/edition-mot-de-passe/' . $user->getId());

        $updatedUser = $this->entityManager->getRepository(User::class)->find($user->getId());
        $this->assertTrue($this->userPasswordHasher->isPasswordValid($updatedUser, 'newSecurePassword'));
    }

    private function createUser(string $pseudo, string $password = 'password'): User
    {
        $user = new User();
        $user->setEmail($pseudo . '@example.com');
        $user->setFullName('Test User');
        $user->setPseudo($pseudo);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
