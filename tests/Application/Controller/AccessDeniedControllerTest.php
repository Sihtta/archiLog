<?php

namespace Tests\Application\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccessDeniedControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/accessdenied');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('html');
    }
}
