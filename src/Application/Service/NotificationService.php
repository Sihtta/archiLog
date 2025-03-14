<?php

namespace App\Application\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class NotificationService
{
    private HttpClientInterface $httpClient;
    private string $discordWebhookUrl;

    public function __construct(HttpClientInterface $httpClient, string $discordWebhookUrl)
    {
        $this->httpClient = $httpClient;
        $this->discordWebhookUrl = $discordWebhookUrl;
    }

    // Cette méthode update implémente le comportement de l'Observer pour notifier les changements
    public function update(string $message): void
    {
        $this->sendTaskStatusUpdate($message);
    }

    // Méthode qui envoie réellement la notification via Discord
    public function sendTaskStatusUpdate(string $message): void
    {
        if (empty($this->discordWebhookUrl)) {
            return;
        }

        try {
            $this->httpClient->request('POST', $this->discordWebhookUrl, [
                'json' => ['content' => $message],
            ]);
        } catch (\Exception $e) {
            // Gérer l'erreur
        }
    }
}
