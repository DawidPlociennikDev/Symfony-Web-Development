<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TasksTest extends WebTestCase
{

    public $client;

    public function setUp(): void 
    {   
        $this->client = static::createClient();
    }

    public function testSeeImportantElementsOnPage(): void
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'My To Do List');
        $this->assertSelectorTextContains('button', 'Add a task');
    }

    public function testCanCreateNewTask(): void
    {
        $crawler = $this->client->request('GET', '/');

        $form = $crawler->selectButton('Add a task')->form();
        $form['title'] = 'Testing task';

        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('ul:contains("Testing task")')->count(), 'User create new task with title "Testing task"');
    }

    public function testSwitchStatusTask(): void
    {
        $crawler = $this->client->request('GET', '/');

        $switchStatus = $crawler->filter('a:contains("Testing task")')->link();
        $crawler = $this->client->click($switchStatus);
        $crawler = $this->client->followRedirect();
        $this->assertSelectorTextContains('.checked', 'Testing task', 'User switch status of task');
    }

    public function testDeleteTask(): void
    {
        $crawler = $this->client->request('GET', '/');

        $deleteLink = $crawler->filter('a:contains("X")')->link();
        $crawler = $this->client->click($deleteLink);
        $crawler = $this->client->followRedirect();
        $this->assertSelectorTextNotContains('ul', 'Testing task');
    }
}
