<?php

namespace App\Controller;

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
    
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $password_encoder, SessionInterface $session): Response
    {
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

            $this->manager->persist($user);
            $this->manager->flush();

            $this->LoginUserAutomatically($user, $password, $session);
            return $this->redirectToRoute('admin_main_page');
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
