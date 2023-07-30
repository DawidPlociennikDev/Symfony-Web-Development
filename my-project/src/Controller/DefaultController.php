<?php

namespace App\Controller;

use App\Entity\Pdf;
use App\Entity\User;
use App\Entity\Video;
use App\Entity\Address;
use App\Entity\Author;
use App\Entity\File;
use App\Events\VideoCreatedEvent;
use App\Form\VideoFormType;
use App\Services\GiftsService;
use App\Services\MyService;
use App\Services\ServiceInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DefaultController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $entityManager, GiftsService $gifts, $logger, EventDispatcherInterface $dispatcher)
    {
        // user service $logger;
        $this->entityManager = $entityManager;
        $gifts->gifts = ['a', 'b', 'c', 'd'];
        $this->dispatcher = $dispatcher;
    }

    #[Route('/home', name: 'home')]
    public function index(GiftsService $gifts, Request $request, SessionInterface $session, User $user): Response
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


        // CREATE
        // $user = new User();
        // $user->setName('Robert');
        // $this->entityManager->persist($user);
        // $this->entityManager->flush();

        // dump('A new user was saved with the id of ' . $user->getId());

        // $repository = $this->entityManager->getRepository(User::class);

        // FIND
        // $user = $repository->find(1);
        // $user = $repository->findOneBy(['name' => 'Robert']);
        // $user = $repository->findOneBy(['id' => 5, 'name' => 'Robert']);
        // $user = $repository->findBy(['name' => 'Robert'], ['id' => 'DESC']);
        // $user = $repository->findAll();

        // UPDATE
        // $id = 1;
        // $user = $repository->find($id);
        // if (!$user)
        // {
        //     throw $this->createNotFoundException(
        //         'No user found for id ' . $id
        //     );
        // }
        // $user->setName('New user name!');
        // $this->entityManager->flush();

        // DELETE
        // $id = 2;
        // $user = $repository->find($id);
        // $this->entityManager->remove($user);
        // $this->entityManager->flush();

    
        // dump($user);

        // new method own query
        $conn = $this->entityManager->getConnection();
        $sql = '
            SELECT * FROM user u
            WHERE u.id > :id
        ';
        $stmt = $conn->prepare($sql);

        dump($stmt->executeQuery(['id'=>3])->fetchAllAssociative());


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


    #[Route('/user/{id}', name: 'user_by_id')]
    public function getUserById(Request $request, User $user) : Response
    {
        dump($user);
        return $this->render('default/clear.html.twig', [
        ]);
    }


    #[Route('/doctrine', name: 'doctrine')]
    public function doctrine(Request $request) : Response
    {
        $user = new User();
        $user->setName('Robert');
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        return $this->render('default/clear.html.twig', []);
    }


    #[Route('/video', name: 'video')]
    public function video(Request $request) : Response
    {
        $user = new User();
        $user->setName('Robert');
        for ($i=1; $i <= 3; $i++) { 
            $video = new Video();
            $video->setTitle('Video title - ' . $i);
            $user->addVideo($video);
            $this->entityManager->persist($video);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // dump('Created a video with the id of ' . $video->getId());
        // dump('Created a video with the id of ' . $user->getId());
        
        // $video = $this->entityManager->getRepository(Video::class)->find(1);
        // dump($video->getUser());
        // dump($video->getUser()->getName());
        // $user = $this->entityManager->getRepository(User::class)->find(1);

        // foreach ($user->getVideos() as $video) {
        //     dump($video->getTitle());
        // }

        return $this->render('default/clear.html.twig', []);
    }


    #[Route('/delete_user', name: 'delete_user')]
    public function delete_user(Request $request) : Response
    {
        $user = $this->entityManager->getRepository(User::class)->find(1);
        $video = $this->entityManager->getRepository(Video::class)->find(1);

        $user->removeVideo($video);
        $this->entityManager->flush();

        foreach ($user->getVideos() as $video) {
            dump($video->getTitle());
        }

        // $this->entityManager->remove($user);
        // $this->entityManager->flush();
        // dump($user);

        return $this->render('default/clear.html.twig', []);
    }


    #[Route('/address', name: 'address')]
    public function address(Request $request) : Response
    {
        $user = new User();
        $user->setName('John');
        $address = new Address();
        $address->setStreet('street');
        $address->setNumber(23);
        $user->setAddress($address);

        $this->entityManager->persist($user);
        $this->entityManager->persist($address);
        $this->entityManager->flush();

        dump($user->getAddress()->getStreet());


        return $this->render('default/clear.html.twig', []);
    }

    #[Route('/followed', name: 'followed')]
    public function followed(Request $request) : Response
    {
        // for ($i=1; $i <= 4; $i++) { 
        //     $user = new User();
        //     $user->setName('Robert - ' . $i);
        //     $this->entityManager->persist($user);
        // }

        // $this->entityManager->flush();
        // dump('last user id - ' . $user->getId());

        $user1 = $this->entityManager->getRepository(User::class)->find(1);
        // $user2 = $this->entityManager->getRepository(User::class)->find(2);
        // $user3 = $this->entityManager->getRepository(User::class)->find(3);
        $user4 = $this->entityManager->getRepository(User::class)->find(4);

        // $user1->addFollowed($user2);
        // $user1->addFollowed($user3);
        // $user1->addFollowed($user4);

        // $this->entityManager->flush();

        dump($user1->getFollowed()->count());
        dump($user1->getFollowing()->count());
        dump($user4->getFollowing()->count());

        return $this->render('default/clear.html.twig', []);
    }
    
    #[Route('/doctrine_entities', name: 'doctrine_entities')]
    public function doctrine_entities(Request $request) : Response
    {
        // $items = $this->entityManager->getRepository(File::class)->find(1);
        $author = $this->entityManager->getRepository(Author::class)->findByIdWithPdf(1);
        dump($author);
        foreach ($author->getFiles() as $file) {
            // if ($file instanceof Pdf)
                dump($file->getFilename());
        }

        return $this->render('default/clear.html.twig', []);
    }
    
    #[Route('/cache', name: 'cache')]
    public function cache()
    {
        $cache = new FilesystemAdapter();
        $posts = $cache->getItem('database.get_posts');

        if (!$posts->isHit())
        {
            $posts_from_db = ['post 1', 'post 2', 'post 3'];
            dump('connected with database...');

            $posts->set(serialize($posts_from_db));
            $posts->expiresAfter(15);
        }
        $cache->deleteItem('database.get_posts');

        $cache->clear();

        dump(unserialize($posts->get()));

        return $this->render('default/clear.html.twig', []);
    }
    
    #[Route('/cache2', name: 'cache2')]
    public function cache2()
    {
        $cache = new TagAwareAdapter(
            new FilesystemAdapter()
        );


        $acer = $cache->getItem('acer');
        $dell = $cache->getItem('dell');
        $ibm = $cache->getItem('ibm');
        $apple = $cache->getItem('apple');

        if (!$acer->isHit())
        {
            $acer_from_db = 'acer laptop';
            $acer->set($acer_from_db);
            $acer->tag(['computers', 'laptops', 'acer']);
            $cache->save($acer);
            dump('acer laptop from database');
        }

        if (!$dell->isHit())
        {
            $dell_from_db = 'dell laptop';
            $dell->set($dell_from_db);
            $dell->tag(['computers', 'laptops', 'dell']);
            $cache->save($dell);
            dump('dell laptop from database');
        }

        if (!$ibm->isHit())
        {
            $ibm_from_db = 'ibm laptop';
            $ibm->set($ibm_from_db);
            $ibm->tag(['computers', 'desktops', 'ibm']);
            $cache->save($ibm);
            dump('ibm laptop from database');
        }

        if (!$apple->isHit())
        {
            $apple_from_db = 'apple laptop';
            $apple->set($apple_from_db);
            $apple->tag(['computers', 'desktops', 'apple']);
            $cache->save($apple);
            dump('apple laptop from database');
        }

        // $cache->invalidateTags(['ibm']);
        // $cache->invalidateTags(['desktops']);
        // $cache->invalidateTags(['laptops']);
        $cache->invalidateTags(['computers']);

        dump($acer->get());
        dump($dell->get());
        dump($ibm->get());
        dump($apple->get());

        return $this->render('default/clear.html.twig', []);
    }
    
    #[Route('/event', name: 'event')]
    public function event(Request $request)
    {
        $video = new \stdClass();
        $video->title = 'Funny movie';
        $video->category = 'funny';

        $event = new VideoCreatedEvent($video);
        $this->dispatcher->dispatch($event, 'video.created.event');

        return $this->render('default/clear.html.twig', []);
    }
    
    #[Route('/form', name: 'form')]
    public function form(Request $request)
    {
        $video = new Video();

        // $video->setTitle('Write a blog post');
        // $video->setCreatedAt(new DateTime('tomorrow'));
        // $videos = $this->entityManager->getRepository(Video::class)->findAll();
        // dump($videos);

        // $video = $this->entityManager->getRepository(Video::class)->find(1);

        $form = $this->createForm(VideoFormType::class, $video);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $file = $form->get('file')->getData();
            $fileName = sha1(random_bytes(14)).'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('videos_directory'),
                $fileName
            );
            $video->setFile($fileName);
            $this->entityManager->persist($video);
            $this->entityManager->flush();
            return $this->redirectToRoute('form');
        }

        return $this->render('default/clear.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    #[Route('/service', name: 'service')]
    public function service(Request $request, ServiceInterface $service)
    {
        // $user = $this->entityManager->getRepository(User::class)->find(1);
        // $user->setName('Rob');
        // $this->entityManager->persist($user);
        // $this->entityManager->flush();

        return $this->render('default/clear.html.twig', []);
    }
    
    #[Route('/eager', name: 'eager')]
    public function eager(Request $request) : Response
    {
        // $user = new User();
        // $user->setName('Robert');

        // for ($i=1; $i <= 3 ; $i++) { 
        //     $video = new Video();
        //     $video->setTitle('Video title - ' . $i);
        //     $user->addVideo($video);
        //     $this->entityManager->persist($video);
        // }

        // $this->entityManager->persist($user);
        // $this->entityManager->flush();

        $user = $this->entityManager->getRepository(User::class)->findWithVideos(1);
        dump($user);

        return $this->render('default/clear.html.twig', []);
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

    public function mostPopularPosts($number = 3) 
    {
        // database call
        $posts = ['post 1', 'post 2', 'post 3', 'post 4'];
        return $this->render('default/most_popular_posts.html.twig', [
            'posts' => $posts
        ]);
    }
}
