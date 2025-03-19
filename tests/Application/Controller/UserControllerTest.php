<?php

namespace Tests\Application\Controller;

use App\Domain\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private User $user;
    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        $client = static::createClient();
        $this->entityManager = $client->getContainer()->get('doctrine')->getManager();
        $this->passwordHasher = $client->getContainer()->get(UserPasswordHasherInterface::class);

        // Vérifier si l'utilisateur existe déjà en base
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin2@portfolio.fr']);

        if (!$existingUser) {
            $this->user = new User();
            $this->user->setEmail('admin2@portfolio.fr');
            $this->user->setPseudo('test');
            $this->user->setPassword($this->passwordHasher->hashPassword($this->user, '1234'));
            $this->user->setRoles(['ROLE_USER']);

            $this->entityManager->persist($this->user);
            $this->entityManager->flush();
        } else {
            $this->user = $existingUser;
        }
    }

    public function testEditPasswordRedirectsIfNotLoggedIn()
    {
        $client = static::createClient();
        
        // Accès sans être connecté
        $client->request('GET', '/utilisateur/edition-mot-de-passe/' . $this->user->getId());
        
        // Vérifier qu'on est bien redirigé vers la page de connexion
        $this->assertResponseRedirects('/connexion');
    }

    public function testEditPasswordAsAuthenticatedUser()
    {
        $client = static::createClient();
        
        // Se connecter avec admin2@portfolio.fr
        $client->request('POST', '/connexion', [
            'email' => 'admin2@portfolio.fr',
            'password' => '1234',
        ]);

        // Vérifier que la connexion a fonctionné
        $this->assertResponseRedirects('/');

        // Accéder à la page d'édition de mot de passe
        $client->request('GET', '/utilisateur/edition-mot-de-passe/' . $this->user->getId());
        
        // Vérifier que la page est bien accessible
        $this->assertResponseIsSuccessful();
    }
}