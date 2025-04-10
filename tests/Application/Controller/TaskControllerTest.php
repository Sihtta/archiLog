<?php

namespace Tests\Application\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testIndexRedirectsIfNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');

        $this->assertResponseRedirects('/connexion');
    }

    public function testCreateTaskRequiresAuthentication(): void
{
    $client = static::createClient();
    $client->request('GET', '/tasks/new');

    $this->assertResponseRedirects('/connexion');
}

    public function testMoveTaskWithInvalidStatus(): void
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/1/move/invalid_status');

        $this->assertResponseStatusCodeSame(404);
    }
}