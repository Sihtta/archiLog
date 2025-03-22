<?php

namespace App\Domain\User\Repository;

use App\Domain\User\Entity\UsernameHistory;

interface UsernameHistoryRepositoryInterface
{
    public function find($id, $lockMode = null, $lockVersion = null): ?UsernameHistory;

    public function findOneBy(array $criteria, array $orderBy = null): ?UsernameHistory;

    public function findAll(): array;

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array;
}
