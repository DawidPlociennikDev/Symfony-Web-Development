<?php

namespace App\Services;

class MySecondService implements ServiceInterface
{
    public function __construct()
    {
        dump('from second service');
        $this->doSomething();
    }

    public function doSomething()
    {

    }

    public function doSomething2()
    {
        return 'doSomething2';
    }

    public function someMethod()
    {
        return 'Hello!';
    }
}
