<?php

namespace Tests\Application\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PomodoroControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pomodoro');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('html');
    }

    public function testChangeStrategyWithValidStrategy(): void
    {
        $client = static::createClient();

        $client->request('GET', '/pomodoro/strategy/short');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testChangeStrategyWithInvalidStrategy(): void
    {
        $client = static::createClient();
        $client->request('GET', '/pomodoro/strategy/invalid');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
