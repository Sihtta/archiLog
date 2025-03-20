<?php

namespace Tests\Application\Service;

use App\Application\Service\NotificationService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class NotificationServiceTest extends TestCase
{
    public function testUpdateCallsSendTaskStatusUpdate()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://discord-webhook-url',
                ['json' => ['content' => 'Test message']]
            );

        $notificationService = new NotificationService($httpClient, 'https://discord-webhook-url');
        $notificationService->update('Test message');
    }

    public function testSendTaskStatusUpdateHandlesEmptyWebhookUrl()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->never())->method('request');

        $notificationService = new NotificationService($httpClient, '');
        $notificationService->sendTaskStatusUpdate('Test message');
    }

    public function testSendTaskStatusUpdateHandlesException()
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('request')
            ->willThrowException($this->createMock(TransportExceptionInterface::class));

        $notificationService = new NotificationService($httpClient, 'https://discord-webhook-url');

        $notificationService->sendTaskStatusUpdate('Test message');
        
        $this->assertTrue(true);
    }
}