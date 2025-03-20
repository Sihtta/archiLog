<?php

namespace Tests\Application\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');

        $this->assertResponseIsSuccessful();
    }

    public function testRegistrationPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/inscription');

        $this->assertResponseIsSuccessful();
    }

    public function testRedirectAfterLoginForUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/redirect-after-login');

        $this->assertResponseRedirects('/');
    }
}