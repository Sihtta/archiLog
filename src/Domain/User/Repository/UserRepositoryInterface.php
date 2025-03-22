<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

interface UserRepositoryInterface
{
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void;

    public function findOneByEmail(string $email): ?User;

    public function findTopUsersByExp(int $limit = 10): array;
}
