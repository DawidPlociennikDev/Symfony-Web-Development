<?php

namespace App\Controller;

use App\Controller\Traits\SaveSubscription;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    use SaveSubscription;
    
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/register/{plan}', name: 'register', defaults:['plan' => null])]
    public function register(Request $request, UserPasswordHasherInterface $password_encoder, SessionInterface $session, $plan): Response
    {

        if ($request->isMethod('GET')) {
            $session->set('planName', $plan);
            $session->set('planPrice', Subscription::getPlanDataPriceByname($plan));
        }


        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setName($request->get('user')['name']);
            $user->setLastName($request->get('user')['last_name']);
            $user->setEmail($request->get('user')['email']);
            $password = $password_encoder->hashPassword($user, $request->get('user')['password']['first']);
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);

            $date = new \DateTime();
            $date->modify('+1 month');
            $subscription = new Subscription();
            $subscription->setValidTo($date);
            $subscription->setPlan($session->get('planName'));

            if ($plan == Subscription::getPlanDataNameByIndex(0)) {
                $subscription->setFreePlanUsed(true);
                $subscription->setPaymentStatus('paid');
            }

            $user->setSubscription($subscription);
            
            $this->manager->persist($user);
            $this->manager->flush();

            $this->LoginUserAutomatically($user, $password, $session);
            return $this->redirectToRoute('admin_main_page');
        }

        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED') && $plan == Subscription::getPlanDataNameByIndex(0)) {
            $this->saveSubscription($plan, $this->getUser());
            return $this->redirectToRoute('admin_main_page');
        } elseif ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('payment');
        }

        return $this->render('front/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $helper): Response
    {
        return $this->render('front/login.html.twig', [
            'error' => $helper->getLastAuthenticationError()
        ]);
    }

    private function LoginUserAutomatically($user, $password, $session)
    {
        $token = new UsernamePasswordToken(
            $user,
            $password,
            $user->getRoles()
        );

        $this->container->get('security.token_storage')->setToken($token);
        $session->set('_security_main', serialize($token));
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
