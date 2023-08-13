<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerVideoTest extends WebTestCase
{

    protected $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->client->disableReboot();
    }

    public function testNoResults(): void
    {
        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/');

        $form = $crawler->selectButton('Search video')->form([
            'query' => 'aaa'
        ]);

        $crawler = $this->client->submit($form);

        $this->assertStringContainsString('No results were found', $crawler->filter('h1')->text());
    }

    public function testResults(): void
    {
        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/');

        $form = $crawler->selectButton('Search video')->form([
            'query' => 'Movies'
        ]);

        $crawler = $this->client->submit($form);

        $this->assertGreaterThan(4, $crawler->filter('h3')->count());
    }

    public function testSorting(): void
    {
        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/');

        $form = $crawler->selectButton('Search video')->form([
            'query' => 'Movies',
        ]);
        $crawler = $this->client->submit($form);

        $form = $crawler->filter('#form-sorting')->form([
            'sortby' => 'desc',
        ]);

        $crawler = $this->client->submit($form);

        $this->assertEquals('Movies 9', $crawler->filter('h3')->first()->text());
    }
}
