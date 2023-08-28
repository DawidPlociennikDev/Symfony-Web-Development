<?php

namespace App\Utils;

use App\Utils\Interfaces\CacheInterface;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\SQLite3Cache;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;

class FilesCache implements CacheInterface {

    public $cache;
    public function __construct()
    {
        $provider = new SQLite3Cache(new \SQLite3(__DIR__ . '/cache/data.db'), 'TableName');

        $this->cache =  new TagAwareAdapter(
            new DoctrineAdapter(
                $provider,
                $namespace = '',
                $defaultLifetime = 0
            )
        );
    }
}
