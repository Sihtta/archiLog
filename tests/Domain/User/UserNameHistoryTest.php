<?php

namespace Tests\Domain\User;

use PHPUnit\Framework\TestCase;
use App\Domain\User\Entity\UsernameHistory;
use App\Domain\User\Entity\User;
use DateTimeImmutable;

class UsernameHistoryTest extends TestCase
{
    public function testUsernameHistoryCreation()
    {
        $user = $this->createMock(User::class);
        $history = new UsernameHistory();
        $history->setUser($user)
                ->setOldPseudo('OldName')
                ->setNewPseudo('NewName')
                ->setChangedAt(new DateTimeImmutable());

        $this->assertInstanceOf(UsernameHistory::class, $history);
        $this->assertEquals('OldName', $history->getOldPseudo());
        $this->assertEquals('NewName', $history->getNewPseudo());
        $this->assertNotNull($history->getChangedAt());
    }
}
