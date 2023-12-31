<?php

namespace App\Controller;

use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SubscriptionController extends AbstractController
{

    
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/pricing', name: 'pricing')]
    public function pricing(): Response
    {
        return $this->render('front/pricing.html.twig', [
            'name' => Subscription::getPlanDataNames(),
            'price' => Subscription::getPlanDataPrices()
        ]);
    }

    #[Route('/payment/{paypal}', name: 'payment', defaults: ['paypal' => false])]
    public function payment($paypal, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        if($session->get('planName') == 'enterprise') {
            $subscribe = Subscription::EnterprisePlan;
        } else {
            $subscribe = Subscription::ProPlan;
        }

        return $this->render('front/payment.html.twig', ['subscribe'=>$subscribe]);
    }
}
