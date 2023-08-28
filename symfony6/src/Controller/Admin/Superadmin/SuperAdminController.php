<?php

namespace App\Controller\Admin\Superadmin;

use App\Entity\User;
use App\Entity\Video;
use App\Form\VideoType;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use App\Utils\Interfaces\UploaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;


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

    // #[Route('/upload-video', name: 'upload_video')]
    // public function uploadVideo(Request $request): Response
    // {
    //     return $this->render('admin/upload_video.html.twig');
    // }

    #[Route('/upload-video-locally', name: 'upload_video_locally')]
    public function uploadVideoLocally(Request $request, UploaderInterface $fileUploader)
    {
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('uploaded_video')->getData();
            $fileName = $fileUploader->upload($file);
            $base_path = Video::uploadFolder;
            $video->setPath($base_path . $fileName[0]);
            $video->setTitle($fileName[1]);

            

            $this->manager->persist($video);
            $this->manager->flush();
        }

        return $this->render('admin/upload_video_locally.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/upload-video-by-vimeo', name: 'upload_video_by_vimeo')]
    public function uploadVideoByVimeo(Request $request)
    {
        $vimeo_id = preg_replace('/^\/.+\//', '', $request->get('video_uri'));
        if ($request->get('videoName') && $vimeo_id) {
            $video = new Video();
            $video->setTitle($request->get('videoName'));
            $video->setPath(Video::VimeoPath . $vimeo_id);

            $this->manager->persist($video);
            $this->manager->flush();

            return $this->redirectToRoute('videos');
        }
        return $this->render('admin/upload_video_vimeo.html.twig');
    }

    #[Route('/set-video-duration/{video}/{vimeo_id}', name: 'set_video_duration', requirements: ['vimeo_id' => '.+'])]
    public function setVideoDuration(Video $video, $vimeo_id)
    {
        if (!is_numeric($vimeo_id)) {


            return $this->redirectToRoute('videos');
        }

        $user_vimeo_token = $this->getUser()->getVimeoApiKey();
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.vimeo.com/videos/{$vimeo_id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/vnd.vimeo.*+json;version=3.4",
                "Authorization: Bearer $user_vimeo_token",
                "Cache-Control: no-cache",
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new ServiceUnavailableHttpException('Error. Try again later. Message: ' . $err);
        } else {
            $duration =  json_decode($response, true)['duration'] / 60;
            if ($duration) {
                $video->setDuration($duration);
                $this->manager->persist($video);
                $this->manager->flush();
            } else {
                $this->addFlash('danger', 'We were not able to update duration. Check the video.');
            }
            return $this->redirectToRoute('videos');
        }
    }

    #[Route('/delete-video/{video}/{path}', name: 'delete_video', requirements: ['path' => '.+'])]
    public function deleteVideo(Video $video, $path, UploaderInterface $fileUploader)
    {
        $this->manager->remove($video);
        $this->manager->flush();

        if ($fileUploader->delete($path)) {
            $this->addFlash('success', 'The video was successfully deleted.');
        } else {
            $this->addFlash('danger', 'We were not able to delete. Check the video.');
        }

        return $this->redirectToRoute('videos');
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

    #[Route('/update-video-category/{video}', name: 'update_video_category', methods: ['POST'])]
    public function updateVideoCategory(Request $request, Video $video)
    {
        $category = $this->manager->getRepository(Category::class)->find($request->get('video_category'));

        $video->setCategory($category);

        $this->manager->persist($video);
        $this->manager->flush();

        return $this->redirectToRoute('videos');
    }
}
