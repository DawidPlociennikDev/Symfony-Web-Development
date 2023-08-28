<?php

namespace App\Listeners;

use App\Entity\User;
use App\Entity\Video;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class NewVideoListener
{

    public $templating;
    public $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // only act on some "Product" entity
        if (!$entity instanceof Video) {
            return;
        }


        $entityManager = $args->getObjectManager();
        // ... do something with the Product

        $users = $entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $message = (new TemplatedEmail())
                ->from('send@example.com')
                ->to($user->getEmail())
                ->subject('New video')
                ->text('Sending emails is fun again!')
                ->htmlTemplate('emails/new_video.html.twig')
                ->context([
                    'name' => $user->getName(),
                    'video' => $entity,
                ]);
    
            $this->mailer->send($message);
        }
    }
}
