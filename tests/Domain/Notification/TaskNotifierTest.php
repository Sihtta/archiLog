<?php

namespace Tests\Domain\Notification;

use App\Domain\Notification\TaskNotifier;
use App\Domain\Notification\Observer\NotificationObserverInterface;
use PHPUnit\Framework\TestCase;

class TaskNotifierTest extends TestCase
{
    public function testAddObserverAndNotifyObservers()
    {
        $notifier = new TaskNotifier();

        $observerMock = $this->createMock(NotificationObserverInterface::class);
        $observerMock->expects($this->once())->method('notify')->with('Test message');

        $notifier->addObserver($observerMock);
        $notifier->notifyObservers('Test message');
    }
}
