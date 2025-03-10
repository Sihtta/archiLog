<?php

namespace App\Infrastructure\Notification\Observer;

use App\Domain\Notification\Observer\NotificationObserverInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscordNotifier implements NotificationObserverInterface
{
    private string $webhookUrl;
    private HttpClientInterface $httpClient;

    public function __construct(string $webhookUrl, HttpClientInterface $httpClient)
    {
        $this->webhookUrl = $webhookUrl;
        $this->httpClient = $httpClient;
    }

    public function notify(string $message): void
    {
        $this->httpClient->request('POST', $this->webhookUrl, [
            'json' => ['content' => $message],
        ]);
    }
}
