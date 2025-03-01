<?php

namespace App\DataFixtures;

use App\Domain\User\Entity\User;
use App\Domain\Task\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserTaskFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Générateur de données en français
        $users = [];

        // Création de 5 utilisateurs
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email)
                ->setFullName($faker->name)
                ->setPseudo($faker->userName)
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->passwordHasher->hashPassword($user, 'password'));

            $manager->persist($user);
            $users[] = $user;
        }

        // Création de 10 tâches aléatoires
        for ($i = 1; $i <= 10; $i++) {
            $task = new Task();
            $task->setTitle($faker->sentence(3))
                ->setDescription($faker->paragraph)
                ->setStatus($faker->randomElement(['pending', 'in_progress', 'completed']))
                ->setUser($faker->randomElement($users));

            $manager->persist($task);
        }

        // Envoi en base
        $manager->flush();
    }
}
