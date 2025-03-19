<?php

namespace App\Tests\EntityListener;

use App\Domain\User\Entity\User;
use App\EntityListener\UserListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListenerTest extends TestCase
{
    public function testPrePersistEncodesPassword()
    {
        $user = new User();
        $user->setPlainPassword('password123');
        
        $hasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $hasherMock->expects($this->once())
            ->method('hashPassword')
            ->with($user, 'password123')
            ->willReturn('hashed_password');
        
        $listener = new UserListener($hasherMock);
        $listener->prePersist($user);
        
        $this->assertEquals('hashed_password', $user->getPassword());
        $this->assertNull($user->getPlainPassword());
    }

    public function testPreUpdateEncodesPassword()
    {
        $user = new User();
        $user->setPlainPassword('newpassword');
        
        $hasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $hasherMock->expects($this->once())
            ->method('hashPassword')
            ->with($user, 'newpassword')
            ->willReturn('new_hashed_password');
        
        $listener = new UserListener($hasherMock);
        $listener->preUpdate($user);
        
        $this->assertEquals('new_hashed_password', $user->getPassword());
        $this->assertNull($user->getPlainPassword());
    }

    public function testEncodePasswordDoesNothingIfNoPlainPassword()
{
    $user = new User();
    $existingPassword = 'password';
    $user->setPassword($existingPassword);

    $hasherMock = $this->createMock(UserPasswordHasherInterface::class);
    $hasherMock->expects($this->never())
        ->method('hashPassword');

    $listener = new UserListener($hasherMock);
    $listener->encodePassword($user);

    $this->assertSame($existingPassword, $user->getPassword());
}
}