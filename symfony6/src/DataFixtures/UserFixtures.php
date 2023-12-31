<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public $password_encoder;
    public function __construct(UserPasswordHasherInterface $password_encoder)
    {
        $this->password_encoder = $password_encoder;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$name, $last_name, $email, $password, $api_key, $roles]) {
            $user = new User();
            $user->setName($name);
            $user->setLastName($last_name);
            $user->setEmail($email);
            $user->setPassword($this->password_encoder->hashPassword($user, $password));
            $user->setVimeoApiKey($api_key);
            $user->setRoles($roles);
            $manager->persist($user);
        }
        $manager->flush();
    }

    private function getUserData(): array 
    {
        return [
            ['John', 'Wayne', 'jw@symf6.loc', 'pass', 'cc9a2765b47db427efdb5f708e52f08d', ['ROLE_ADMIN']],
            ['John', 'Wayne2', 'jw2@symf6.loc', 'pass', null, ['ROLE_ADMIN']],
            ['John', 'Doe', 'jd@symf6.loc', 'pass', null, ['ROLE_USER']],
            ['Ted', 'Bundy', 'tb@symf6.loc', 'pass', null, ['ROLE_USER']],
        ];
    }
}
