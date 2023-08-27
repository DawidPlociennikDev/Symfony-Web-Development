<?php

namespace App\Controller\Admin\Superadmin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin/su")
 */

class SuperAdminController extends AbstractController
{

    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/upload-video', name: 'upload_video')]
    public function uploadVideo(): Response
    {
        return $this->render('admin/upload_video.html.twig');
    }

    #[Route('/users', name: 'users')]
    public function users(): Response
    {
        $repository = $this->manager->getRepository(User::class);
        $users = $repository->findBy([], ['name' => 'ASC']);

        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/delete-user/{user}', name: 'delete_user')]
    public function deleteUser(User $user)
    {
        $this->manager->remove($user);
        $this->manager->flush();
        return $this->redirectToRoute('users');
    }
}
