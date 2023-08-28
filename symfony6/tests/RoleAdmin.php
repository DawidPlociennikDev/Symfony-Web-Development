<?php

namespace App\Tests;

trait RoleAdmin
{
    
    protected $entityManager;
    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = self::$kernel->getContainer();
        $container = self::$container;
        $cache = self::$container->get('App\Utils\Interfaces\CacheInterface');
        $this->cache = $cache->cache;
        $this->cache->clear();

        $this->client = static::createClient([], [
            'PHP_AUTH_USER' => 'jw@symf6.loc',
            'PHP_AUTH_PW' => 'pass'
        ]);
        $this->client->disableReboot();

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->cache->clear();
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
            $this->entityManager->close();
            $this->entityManager = null;
        }
    }
}
