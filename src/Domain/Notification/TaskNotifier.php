<?php

namespace App\Domain\Notification;

use App\Domain\Notification\Observer\NotificationObserverInterface;

class TaskNotifier
{
    private array $observers = [];

    public function addObserver(NotificationObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function notifyObservers(string $message): void
    {
        foreach ($this->observers as $observer) {
            $observer->notify($message);
        }
    }
}
