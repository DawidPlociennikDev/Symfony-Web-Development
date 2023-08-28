<?php

namespace App\Controller;

use App\Entity\User;
use App\Utils\PaypalIPN;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Traits\SaveSubscription;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PayPalController extends AbstractController
{
    use SaveSubscription;
    
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/paypal-verify', name: 'paypal_verify', methods: ['POST'])]
    public function paypalVerify(PaypalIPN $paypalIPN)
    {
        $paypalIPN->useSandbox();
        $paypalIPN->usePHPCerts();

        if ($paypalIPN->verifyIPN()) {
            if (isset($_POST['payment_status']) && $_POST['payment_status'] == 'Completed') {
                $planName = $_POST['item_name'];
                $user = $this->manager->getRepository(User::class)->findOneBy(['email' => $_POST['payer_email']]);

                if ($user) $this->saveSubscription($planName, $user);
            } elseif ($_POST['txn_type'] == 'subscr_cancel' || $_POST['txn_type' == 'subscr_eot']) {
                $user = $this->manager->getRepository(User::class)->findByOne(['email' => $_POST['payer_email']]);

                if (!$user) return;

                $subscription = $user->getSubscription();
                $subscription->setPlan('canceled');
                $subscription->setValidTo(new \DateTime());
                $subscription->setPaymentStatus(null);

                $this->manager->flush();
            }
        }
        return new Response();
    }
}
