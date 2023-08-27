<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllersUsersTest extends WebTestCase
{

    use RoleAdmin;

    public function testUserDeleted()
    {
        $this->client->request('GET', '/admin/su/delete-user/4');
        $user = $this->entityManager->getRepository(User::class)->find(4);
        $this->assertNull($user);
    }

}
