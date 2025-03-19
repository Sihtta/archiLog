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
}
