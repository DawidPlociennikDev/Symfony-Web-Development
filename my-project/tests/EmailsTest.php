<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmailsTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', '/mailer');
        $this->assertResponseIsSuccessful();
        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertInstanceOf('Symfony\Bridge\Twig\Mime\TemplatedEmail', $email);
        $this->assertEmailHtmlBodyContains($email, 'Hi Robert');
        $this->assertEmailTextBodyContains($email, 'Sending emails is fun again!');
        $this->assertEmailHasHeader($email, 'from');
        $this->assertEmailHasHeader($email, 'to');
        $this->assertEmailHeaderSame($email, 'from', 'symfony@test.com');
        $this->assertEmailHeaderSame($email, 'to', 'dawid.plociennik13@gmail.com');
    }
}
