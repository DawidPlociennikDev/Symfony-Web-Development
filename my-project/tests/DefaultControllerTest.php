<?php

namespace App\Tests;

use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use function PHPUnit\Framework\assertTrue;

class DefaultControllerTest extends WebTestCase
{

    private $entityManager;
    public $client;

    protected function setUp(): void 
    {
        parent::setUp();
        self::ensureKernelShutdown();
        $this->client = static::createClient();

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $this->entityManager->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    // public function testSomething(): void
    // {
    //     $crawler = $this->client->request('GET', '/for_functional_test');

    //     $this->assertResponseIsSuccessful();
    //     $this->assertSelectorTextContains('h1', 'Hi');

    //     $this->assertGreaterThan(0, $crawler->filter('html:contains("Hi")')->count());
    //     // $this->assertGreaterThan(0, $crawler->filter('h1.class')->count());
    //     $this->assertCount(1, $crawler->filter('h1'));
    //     // $this->assertTrue(
    //     //     $this->client->getResponse()->headers->contains(
    //     //         'Content-Type',
    //     //         'application/json'
    //     //     ),
    //     //     'the "Content-Type" header is "application/json"' // optional message shown on failure
    //     // );
    //     // $this->assertContains('foo', $this->client->getResponse()->getContent());
    //     // $this->assertRegExp('/foo(bar)?/', $this->client->getResponse()->getContent());
    //     // $this->assertTrue($this->client->getResponse()->isSuccessful(), 'response status is 2xx');
    //     // $this->assertTrue($this->client->getResponse()->isNotFound());
    //     // $this->assertEquals(
    //     //     200, // or Symfony\Component\HttpFoundation\Response::HTTP_OK
    //     //     $this->client->getResponse()->getStatusCode()
    //     // );
    //     // $this->assertTrue(
    //     //     $this->client->getResponse()->isRedirect('/demo/contact')
    //     //     // if the redirection URL was generated as an absolute URL
    //     //     // $this->client->getResponse()->isRedirect('http://localhost/demo/contact')
    //     // );
    //     // $this->assertTrue($this->client->getResponse()->isRedirect());
    // }

    // public function testLinks(): void
    // {
    //     $crawler = $this->client->request('GET', '/for_functional_test');

    //     $link = $crawler->filter('a:contains("Awesome link")')->link();

    //     $crawler = $this->client->click($link);
    //     $this->assertStringContainsString('Remember me', $this->client->getResponse()->getContent());
    // }

    // public function testLogin(): void
    // {
    //     $client = static::createClient();
    //     $crawler = $this->client->request('GET', '/login');

    //     $form = $crawler->selectButton('Sign in')->form();
    //     $form['email'] = 'user@user.com';
    //     $form['password'] = 'hash';

    //     $crawler = $this->client->submit($form);
    //     assertTrue(true);

    //     // $crawler = $this->client->followRedirect();

    //     // $this->assertEquals(1, $crawler->filter('a:contains("logout")')->count());

    // }

    // /**
    //  * @dataProvider provideUrls
    //  */
    // public function testUrls($url) : void 
    // {
    //     $client = static::createClient();
    //     $crawler = $this->client->request('GET', $url); 

    //     $this->assertTrue($this->client->getResponse()->isSuccessful());
        
    // }

    // public function provideUrls()
    // {
    //     return [
    //         ['/login'],
    //         ['/for_functional_test']
    //     ];
    // }

    public function testVideo(): void 
    {
        $video = $this->entityManager->getRepository(Video::class)->find(1);
        $this->entityManager->remove($video);
        $this->entityManager->flush();

        $this->assertNull($this->entityManager->getRepository(Video::class)->find(1));
    }
}
