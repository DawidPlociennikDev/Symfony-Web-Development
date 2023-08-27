<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Video;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * @Route("/admin")
 */

class MainController extends AbstractController
{

    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    #[Route('/', name: 'admin_main_page')]
    public function index(Request $request, UserPasswordHasherInterface $password_encoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, ['user' => $user]);
        $form->handleRequest($request);
        $is_invalid = null;
        
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setName($request->get('user')['name']);
            $user->setLastName($request->get('user')['last_name']);
            $user->setEmail($request->get('user')['email']);

            $password = $password_encoder->hashPassword($user, $request->get('user')['password']['first']);
            $user->setPassword($password);
            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', 'Your changes were saved!');
            return $this->redirectToRoute('admin_main_page');
        } elseif ($request->isMethod('post')) {
            $is_invalid = 'is-invalid';
        }

        return $this->render('admin/my_profile.html.twig', [
            'subscription' => $this->getUser()->getSubscription(),
            'form' => $form->createView(),
            'is_invalid' => $is_invalid
        ]);
    }

    #[Route('/cancel-plan', name: 'cancel_plan')]
    public function cancelPlan()
    {
        $user = $this->manager->getRepository(User::class)->find($this->getUser());

        $subscription = $user->getSubscription();
        $subscription->setValidTo(new \DateTime());
        $subscription->setPaymentStatus(null);
        $subscription->setPlan('canceled');

        $this->manager->persist($user);
        $this->manager->persist($subscription);
        $this->manager->flush();

        return $this->redirectToRoute('admin_main_page');
    }

    #[Route('/delete-account', name: 'delete_account')]
    public function deleteAccount()
    {
        $user = $this->manager->getRepository(User::class)->find($this->getUser());
        $this->manager->remove($user);
        $this->manager->flush();

        session_destroy();

        return $this->redirectToRoute('main_page');
    }



    #[Route('/videos', name: 'videos')]
    public function videos(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $videos = $this->manager->getRepository(Video::class)->findAll();
        } else {
            $videos = $this->getUser()->getLikedVideos();
        }
        return $this->render('admin/videos.html.twig', [
            'videos' => $videos
        ]);
    }

    public function getAllCategories(CategoryTreeAdminOptionList $categories, $editedCategory = null)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $categories->getCategoryList($categories->buildTree());
        return $this->render('admin/_all_categories.html.twig', [
            'categories' => $categories,
            'editedCategory' => $editedCategory
        ]);

    }
}
