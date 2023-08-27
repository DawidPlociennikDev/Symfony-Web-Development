<?php

namespace App\Tests;

use App\Entity\Subscription;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerSubscriptionTest extends WebTestCase
{
    use RoleUser;

    /**
     * @dataProvider urlsWithVideo
     */
    public function testLoggedInUserDoesNotSeesTextForNoMembers($url): void
    {
        $this->client->request('GET', $url);
        $this->assertStringNotContainsString('Video for <b>MEMBERS</b> only', $this->client->getResponse()->getContent());
    }

    /**
     * @dataProvider urlsWithVideo
     */
    public function testNotLoggedInUserSeesTextForNoMembers($url): void
    {
        $client = static::createClient();
        $this->client->request('GET', $url);
        $this->assertStringContainsString('Video for <b>MEMBERS</b> only', $this->client->getResponse()->getContent());
    }

    public function urlsWithVideo()
    {
        yield ['/video-list/category/movies,4'];
        yield ['/search-results?query=movies'];
    }

    public function testExpiredSubscription()
    {
        $subscription = $this->entityManager->getRepository(Subscription::class)->find(2);
        $invalid_date = new \DateTime();
        $invalid_date->modify('-1 day');
        $subscription->setValidTo($invalid_date);

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        $this->client->request('GET', '/video-list/category/movies,4');
        $this->assertStringContainsString('Video for <b>MEMBERS</b> only', $this->client->getResponse()->getContent());
    }

    /**
     * @dataProvider urlsWithVideo2
     */
    public function testNotLoggedInUserSeesVideosForNoMembers($url)
    {
        $client = static::createClient();
        $this->client->request('GET', $url);
        $this->assertStringContainsString('https://player.vimeo.com/video/113716040', $this->client->getResponse()->getContent());
    }

    public function urlsWithVideo2()
    {
        yield ['/video-list/category/toys,2/2'];
        yield ['/search-results?query=Movies+3'];
        yield ['/video-details/2#video_comments'];
    }
}
