<?php

namespace Tests\Domain\User;

use PHPUnit\Framework\TestCase;
use App\Domain\User\Entity\UsernameHistory;
use App\Domain\User\Entity\User;
use DateTimeImmutable;

class UsernameHistoryTest extends TestCase
{
    public function testUsernameHistoryDefaultValues()
    {
        $history = new UsernameHistory();
        
        $this->assertNull($history->getId());
        $this->assertNull($history->getUser());
        $this->assertNull($history->getOldPseudo());
        $this->assertNull($history->getNewPseudo());
        $this->assertNull($history->getChangedAt());
    }

    public function testSetAndGetUser()
    {
        $user = $this->createMock(User::class);
        $history = new UsernameHistory();
        $history->setUser($user);

        $this->assertSame($user, $history->getUser());
    }

    public function testSetAndGetOldPseudo()
    {
        $history = new UsernameHistory();
        $history->setOldPseudo('OldName');

        $this->assertEquals('OldName', $history->getOldPseudo());
    }

    public function testSetAndGetNewPseudo()
    {
        $history = new UsernameHistory();
        $history->setNewPseudo('NewName');

        $this->assertEquals('NewName', $history->getNewPseudo());
    }

    public function testSetAndGetChangedAt()
    {
        $date = new DateTimeImmutable();
        $history = new UsernameHistory();
        $history->setChangedAt($date);

        $this->assertSame($date, $history->getChangedAt());
    }

    public function testUsernameHistoryCompleteCreation()
    {
        $user = $this->createMock(User::class);
        $date = new DateTimeImmutable();
        
        $history = (new UsernameHistory())
            ->setUser($user)
            ->setOldPseudo('OldName')
            ->setNewPseudo('NewName')
            ->setChangedAt($date);

        $this->assertSame($user, $history->getUser());
        $this->assertEquals('OldName', $history->getOldPseudo());
        $this->assertEquals('NewName', $history->getNewPseudo());
        $this->assertSame($date, $history->getChangedAt());
    }

    public function testInvalidValues()
    {
        $history = new UsernameHistory();

        $this->expectException(\TypeError::class);
        $history->setOldPseudo(null);
    }
}
