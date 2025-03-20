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

        // Accéder à la page d'édition de l'utilisateur
        $client->request('GET', '/utilisateur/edition/' . $user->getId());
        $this->assertResponseIsSuccessful();

        // Soumettre le formulaire avec les champs corrects
        $client->submitForm('Modifier mon profil', [
            'user[pseudo]' => 'UpdatedUser',  // Pseudo facultatif
            'user[fullName]' => 'Updated Name', // Modifier le nom complet
            'user[plainPassword]' => 'password',  // Mot de passe
        ]);

        $this->assertResponseRedirects('/utilisateur/edition/' . $user->getId());

        // Vérifier la mise à jour de l'utilisateur
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $updatedUser = $entityManager->getRepository(User::class)->find($user->getId());
        $this->assertSame('UpdatedUser', $updatedUser->getPseudo());
        $this->assertSame('Updated Name', $updatedUser->getFullName());
    }

    public function testEditPassword(): void
    {
        $client = static::createClient();
        $user = $this->createUser();
        $client->loginUser($user);

        // Accéder à la page de modification du mot de passe
        $client->request('GET', '/utilisateur/edition-mot-de-passe/' . $user->getId());
        $this->assertResponseIsSuccessful();

        // Soumettre le formulaire avec les champs corrects
        $client->submitForm('Modifier mon mot de passe', [
            'user_password[plainPassword][first]' => 'password', // Premier champ du mot de passe
            'user_password[plainPassword][second]' => 'newpassword', // Deuxième champ du mot de passe
        ]);

        $this->assertResponseRedirects('/utilisateur/edition-mot-de-passe/' . $user->getId());
    }

    public function testAccessDenied(): void
    {
        $client = static::createClient();
        $user = $this->createUser();

        // Essayer d'accéder à la page sans être connecté
        $client->request('GET', '/utilisateur/edition/' . $user->getId());
        $this->assertResponseRedirects('/access-denied');
    }
}
