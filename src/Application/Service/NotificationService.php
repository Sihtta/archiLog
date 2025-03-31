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

    /**
     * Met à jour l'état d'une tâche en envoyant une notification.
     */
    public function update(string $message): void
    {
        $this->sendTaskStatusUpdate($message);
    }

    /**
     * Envoie un message via un webhook Discord.
     */
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
            // Évite que l'application plante en cas d'erreur d'envoi
        }
    }
}