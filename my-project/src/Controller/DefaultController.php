<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\GiftsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, GiftsService $gifts, $logger)
    {
        // user service $logger;
        $this->entityManager = $entityManager;
        $gifts->gifts = ['a', 'b', 'c', 'd'];
    }

    #[Route('/page', name: 'app_default')]
    public function index(GiftsService $gifts, Request $request, SessionInterface $session): Response
    {
        // exit($request->cookies->get('PHPSESSID'));
        // $this->addFlash('notice', 'Your changes were saved');
        // $this->addFlash('warning', 'Your changes were saved');

        // $cookie = new Cookie(
        //     'my_cookie',
        //     'cookie_value',
        //     time() + (2*365*24*60*60)
        // );

        // $res = new Response();
        // $res->headers->setCookie($cookie);
        // $res->headers->clearCookie('my_cookie');
        // $res->send();

        // $session->set('name', 'session value');
        // $session->remove('name');
        // $session->clear();
        // if ($session->has('name'))
        // {
        //     exit($session->get('name'));
        // }

        // exit($request->query->get('page', 'default'));
        // exit($request->server->get('HTTP_HOST'));
        // $request->isXmlHttpRequest();
        // $request->request->get('page');
        // $request->files->get('foo');

        $users = $this->entityManager->getRepository(User::class)->findAll();
        if (!$users) {
            throw $this->createNotFoundException('The users do not exist');
        }
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => $users,
            'random_gift' => $gifts->gifts
        ]);
    }

    #[Route('/generate-url/{param?}', name: 'generate_url')]
    public function generate_url()
    {
        exit($this->generate_url(
            'generate_url',
            array('page' => 10),
            UrlGeneratorInterface::ABSOLUTE_URL
        ));
    }

    #[Route('/download', name: 'download')]
    public function download()
    {
        $path = $this->getParameter('download_directory');
        return $this->file($path.'file.pdf');
    }

    #[Route('/redirect-test', name: 'redirect_test')]
    public function redirectTest()
    {
        return $this->redirectToRoute('route_to_redirect', array('param' => 10));
    }

    #[Route('/url-to-redirect/{param?}', name: 'route_to_redirect')]
    public function methodToRedirect()
    {
        exit('Test redirection');
    }

    #[Route('/forwarding-to-controller', name: 'forwarding')]
    public function forwardintToController()
    {
        $response = $this->forward(
            'App\Controller\DefaultController::methodToForward',
            array('param' => '1')
        );
        return $response;
    }

    #[Route('/url-to-forwad-to/{param?}', name: 'route_to_forward_to')]
    public function methodToForward($param)
    {
        exit('Test controller forwarding - ' . $param);
    }

    #[Route('/blog/{page?}', name: 'blog_list', requirements: ['page' => '\d+'])]
    public function index2(): Response
    {
        return new Response('Optional parameters in url and requirements for parameters');
    }


    #[Route(
        '/articles/{locale}/{year}/{slug}/{category}',
        name: 'articles',
        defaults: ['category' => 'computers'],
        requirements: [
            '_locale' => 'en|fr',
            'category' => 'computers|rtv',
            '_locale' => '\d+',
        ]
    )]
    public function index3(): Response
    {
        return new Response('An advanced route example');
    }


    #[Route(
        ['nl' => '/over-ons', 'en' => '/about-us'],
        name: 'about_us',
    )]
    public function index4(): Response
    {
        return new Response('Translated routes');
    }
}
