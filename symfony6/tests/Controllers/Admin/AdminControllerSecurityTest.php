<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerSecurityTest extends WebTestCase
{

    /**
     * @dataProvider getUrlsForReqgularsUsers
     */
    public function testAccessDeniedForRegularUsers(string $httpMethod, string $url): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'jd@symf6.loc',
            'PHP_AUTH_PW' => 'pass'
        ]);
        $client->request($httpMethod, $url);
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function getUrlsForReqgularsUsers()
    {
        yield ['GET', '/admin/su/categories'];
        yield ['GET', '/admin/su/edit-category/1'];
        yield ['GET', '/admin/su/delete-category/1'];
        yield ['GET', '/admin/su/users'];
        yield ['GET', '/admin/su/upload-video'];
    }

    public function testAdminSu()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'jw@symf6.loc',
            'PHP_AUTH_PW' => 'pass'
        ]);
        $crawler = $client->request('GET', '/admin/su/categories');
        $this->assertSame('Categories list', $crawler->filter('h2')->text());
    }
}
