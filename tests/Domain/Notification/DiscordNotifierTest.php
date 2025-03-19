<?php

namespace Tests\Domain\Notification;

use App\Domain\Notification\Observer\DiscordNotifier;
use App\Domain\Notification\Observer\NotificationObserverInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DiscordNotifierTest extends TestCase
{
    public function testNotifySendsHttpRequest()
    {
        $httpClientMock = $this->createMock(HttpClientInterface::class);
        $httpClientMock->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://example.com/webhook',
                ['json' => ['content' => 'Test message']]
            )
            ->willReturn($this->createMock(ResponseInterface::class));

        $notifier = new DiscordNotifier('https://example.com/webhook', $httpClientMock);
        $notifier->notify('Test message');
    }
}
