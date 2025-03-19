<?php

namespace Tests\Application\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PomodoroControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/pomodoro');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('html'); // Vérifie que la page se charge bien
    }

    public function testChangeStrategyWithValidDuration(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pomodoro/strategy/25');

        $this->assertResponseIsSuccessful();
    }

    public function testChangeStrategyWithInvalidDuration(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pomodoro/strategy/invalid');

        $this->assertResponseStatusCodeSame(500); // Une exception doit être levée
    }
}
