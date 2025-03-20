<?php

namespace App\Tests\Application\Controller;

use App\Domain\User\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class UserControllerTest extends WebTestCase
{
    private function createUser(): User
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $user = new User();
        $user->setEmail('test_' . uniqid() . '@example.com');
        $user->setPseudo('TestUser');
        $user->setPassword('password');
        $user->setFullName('Test User');
        $user->setRoles(['ROLE_USER']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }

    public function testEditUser(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $client->loginUser($user);

        $client->request('GET', '/utilisateur/edition/' . $user->getId());
        $this->assertResponseIsSuccessful();

        $client->submitForm('Modifier mon profil', [
            'user[pseudo]' => 'UpdatedUser',
            'user[fullName]' => 'Updated Name',
            'user[plainPassword]' => 'password',
        ]);

        $this->assertResponseRedirects('/utilisateur/edition/' . $user->getId());

        // Vérifier la mise à jour de l'utilisateur
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $updatedUser = $entityManager->getRepository(User::class)->find($user->getId());
        $this->assertSame('UpdatedUser', $updatedUser->getPseudo());
        $this->assertSame('Updated Name', $updatedUser->getFullName());
    }

    public function testAccessDenied(): void
    {
        $client = static::createClient();
        $user = $this->createUser();

        $client->request('GET', '/utilisateur/edition/' . $user->getId());
        $this->assertResponseRedirects('/access-denied');
    }
}
