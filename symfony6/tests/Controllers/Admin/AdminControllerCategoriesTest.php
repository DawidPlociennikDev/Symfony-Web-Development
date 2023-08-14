<?php

namespace App\Tests;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerCategoriesTest extends WebTestCase
{
    protected $entityManager;
    protected $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient([],[
            'PHP_AUTH_USER' => 'jw@symf6.loc',
            'PHP_AUTH_PW' => 'pass'
        ]);
        $this->client->disableReboot();

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager->beginTransaction();

        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
            $this->entityManager->close();
            $this->entityManager = null;
        }
    }

    public function testTextOnPage(): void
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');
        $this->assertSame('Categories list', $crawler->filter('h2')->text());
        $this->assertStringContainsString('Electronics', $this->client->getResponse()->getContent());
    }

    // public function testNumberOfItems()
    // {
    //     $crawler = $this->client->request('GET', '/admin/su/categories');
    //     $this->assertCount(21, $crawler->filter('option'));
    // }

    // public function testNewCategory()
    // {
    //     $crawler = $this->client->request('GET', '/admin/su/categories');
    //     $form = $crawler->selectButton('Add')->form([
    //         'category[parent]' => 1,
    //         'category[name]' => 'Other electronics'
    //     ]);
    //     $this->client->submit($form);

    //     $category = $this->entityManager->getRepository(Category::class)->findOneBy([
    //         'name' => 'Other electronics'
    //     ]);

    //     $this->assertNotNull($category);
    //     $this->assertSame('Other electronics', $category->getName());
    // }

    // public function testEditCategory()
    // {
    //     $crawler = $this->client->request('GET', '/admin/su/edit-category/1');
    //     $form = $crawler->selectButton('Save')->form([
    //         'category[parent]' => 0,
    //         'category[name]' => 'Electronics2'
    //     ]);
    //     $this->client->submit($form);

    //     $category = $this->entityManager->getRepository(Category::class)->find(1);

    //     $this->assertSame('Electronics2', $category->getName());
    // }

    // public function testDeleteCategory()
    // {
    //     $crawler = $this->client->request('GET', '/admin/su/delete-category/1');
    //     $category = $this->entityManager->getRepository(Category::class)->find(1);
    //     $this->assertNull($category);
    // }
}
