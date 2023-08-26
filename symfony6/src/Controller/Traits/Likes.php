<?php

namespace App\Controller\Traits;
use App\Entity\User;

trait Likes
{
    private function likeVideo($video)
    {
        $user = $this->manager->getRepository(User::class)->find($this->getUser());
        $user->addLikedVideo($video);

        $this->manager->persist($user);
        $this->manager->flush();
        return 'liked';
    }

    private function dislikeVideo($video)
    {
        $user = $this->manager->getRepository(User::class)->find($this->getUser());
        $user->addDislikedVideo($video);

        $this->manager->persist($user);
        $this->manager->flush();
        return 'disliked';
    }

    private function undoLikeVideo($video)
    {
        $user = $this->manager->getRepository(User::class)->find($this->getUser());
        $user->removeLikedVideo($video);

        $this->manager->persist($user);
        $this->manager->flush();
        return 'undo liked';
    }

    private function undoDislikeVideo($video)
    {
        $user = $this->manager->getRepository(User::class)->find($this->getUser());
        $user->removeDislikedVideo($video);

        $this->manager->persist($user);
        $this->manager->flush();
        return 'undo disliked';
    }
}
