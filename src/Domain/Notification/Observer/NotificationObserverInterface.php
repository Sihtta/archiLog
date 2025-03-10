<?php

namespace App\Domain\Notification\Observer;

interface NotificationObserverInterface
{
    public function notify(string $message): void;
}
