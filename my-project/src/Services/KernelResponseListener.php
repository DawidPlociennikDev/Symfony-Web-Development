<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class KernelResponseListener 
{
    public function __invoke(ResponseEvent $event) : void
    {
        $response = new Response('dupa');
        $event->setResponse($response);
    }
}