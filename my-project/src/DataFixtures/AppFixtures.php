<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture 
{
    public function load(ObjectManager $manager) 
    {
        for ($i=0; $i < 5; $i++) { 
            $user = new User();
            $user->setName('name - ', $i);
            $manager->persist($user);
        }

        $manager->flush();
    }
}