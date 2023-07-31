<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToDoListController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_to_do_list')]
    public function index(): Response
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findBy([], ['id' => 'desc']);

        return $this->render('index.html.twig', [
            'tasks' => $tasks
        ]);
    }

    #[Route('/create', name: 'create_task', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $title = trim($request->request->get('title'));
        if (empty($title)) return $this->redirectToRoute('app_to_do_list');

        $task = new Task;
        $task->setTitle($title);
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_to_do_list');
    }

    #[Route('/switch-status/{id}', name: 'switch_status')]
    public function switchStatus($id): Response
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);
        if (empty($task)) return $this->redirectToRoute('app_to_do_list');

        $task->setStatus(!$task->isStatus() ?? 1);
        $this->entityManager->flush();


        return $this->redirectToRoute('app_to_do_list');
    }

    #[Route('/delete/{id}', name: 'delete_task')]
    public function delete(Task $task): Response
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_to_do_list');
    }
}
