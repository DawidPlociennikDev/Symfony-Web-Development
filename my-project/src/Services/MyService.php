<?php

namespace App\Services;
 
use App\Services\MySecondService;
use Doctrine\ORM\Event\PostFlushEventArgs;

class MyService implements ServiceInterface
{
    // use OptionalServiceTrait;
    // public $logger;
    // public $my;

    public function __construct()
    {
        dump('hi');
        // $this->secService = $service;
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        dump('post flush', $args);
    }
    
    public function clear()
    {
        dump('clear...');
    }

    public function someAction()
    {
        // dump($this->logger, $this->my);
    }

    // public function someAction()
    // {
    //     dump($this->service->doSomething2());
    // }
    /**
     * @required
     */
    // public function setSecondService(MySecondService $secondService)
    // {
    //     dump($secondService);
    // }
}
