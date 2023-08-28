<?php

namespace App\Controller;

use App\Controller\Traits\Likes;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Video;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use App\Utils\Interfaces\CacheInterface;
use App\Utils\VideoForNoValidSubscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class FrontController extends AbstractController
{
    use Likes;
    
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/', name: 'main_page')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig', []);
    }

    #[Route('/video-list/category/{categoryName},{id}/{page}', defaults: ['page' => '1'], name: 'video_list')]
    public function videoList(int $id, $page, CategoryTreeFrontPage $categories, Request $request, VideoForNoValidSubscription $video_no_members, CacheInterface $cache): Response
    {

        $cache = $cache->cache;
        $video_list = $cache->getItem('video_list'.$id.$page.$request->get('sortby'));
        $video_list->expiresAfter(60);

        if (!$video_list->isHit()) {
            $ids = $categories->getChildIds($id);
            array_push($ids, $id);
            $videos = $this->manager->getRepository(Video::class)->findByChildsIds($ids, $page, $request->get('sortby'));
            $categories->getCategoryListAndParent($id);
            $response = $this->render('front/video_list.html.twig', [
                'subcategories' => $categories,
                'videos' => $videos,
                'video_no_members' => $video_no_members->check()
            ]);

            $video_list->set($response);
            $cache->save($video_list);
        }


        return $video_list->get();
    }

    #[Route('/video-details/{video}', name: 'video_details')]
    public function videoDetails(VideoRepository $repo, $video, VideoForNoValidSubscription $video_no_members)
    {
        return $this->render('front/video_details.html.twig', [
            'video' => $repo->videoDetails($video),
            'video_no_members' => $video_no_members->check()
        ]);
    }

    #[Route('/new-comment/{video}', methods: 'POST', name: 'new_comment')]
    public function newComment(Video $video, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        if (!empty(trim($request->get('comment')))) {
            $comment = new Comment();
            $comment->setContent($request->get('comment'));
            $comment->setUser($this->getUser());
            $comment->setVideo($video);

            $this->manager->persist($comment);
            $this->manager->flush();
        }
        return $this->redirectToRoute('video_details', ['video' => $video->getId()]);
    }

    #[Route('/search-results/{page}', methods: 'get', defaults: ['page' => '1'], name: 'search_results')]
    public function searchResults($page, Request $request, VideoForNoValidSubscription $video_no_members): Response
    {
        $videos = null;
        $query = null;
        if ($query = $request->get('query')) {
            $videos = $this->manager->getRepository(Video::class)->findByTitle($query, $page, $request->get('sortby'));
            if (!$videos->getItems()) $videos = null;
        }
        return $this->render('front/search_results.html.twig', [
            'videos' => $videos,
            'query' => $query,
            'video_no_members' => $video_no_members->check()
        ]);
    }

    #[Route('/video-list/{video}/like', methods: 'POST', name: 'like_video')]
    #[Route('/video-list/{video}/dislike', methods: 'POST', name: 'dislike_video')]
    #[Route('/video-list/{video}/unlike', methods: 'POST', name: 'undo_like_video')]
    #[Route('/video-list/{video}/undodislike', methods: 'POST', name: 'undo_dislike_video')]
    public function toggleLikesAjax(Video $video, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        switch ($request->get('_route')) {
            case 'like_video':
                $result = $this->likeVideo($video);
                break;
            case 'dislike_video':
                $result = $this->dislikeVideo($video);
                break;
            case 'undo_like_video':
                $result = $this->undoLikeVideo($video);
                break;
            case 'undo_dislike_video':
                $result = $this->undoDislikeVideo($video);
                break;
        }
        return $this->json(['action' => $result, 'id' => $video->getId()]);
    }

    #[Route('/delete-comment/{comment}', name: 'delete_comment')]
    #[Security('user.getId() == comment.getUser().getId()')]
    public function deleteComment(Comment $comment, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $this->manager->remove($comment);
        $this->manager->flush();   

        return $this->redirect($request->headers->get('referer'));
    }


    public function mainCategories()
    {
        $categories = $this->manager->getRepository(Category::class)
            ->findBy(['parent' => null], ['name' => 'ASC']);
        return $this->render('front/_main_categories.html.twig', [
            'categories' => $categories
        ]);
    }
}
