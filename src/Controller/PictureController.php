<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\User;
use App\Repository\PictureRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;   

class PictureController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PictureRepository $pictureRepository,
        private UserRepository $userRepository
    ) {}

    #[Route('/upload/pp/', name: 'upload_photo', methods: ['POST'])]
    public function uploadPhoto(Request $request): Response
    {
        $appEnv = $this->getParameter('app.app_env');
        $UPLOAD_DIR = $this->getParameter('app.upload_dir');
        $API_URL_DEV = $this->getParameter('app.api_url_dev');
        $API_URL_PROD = $this->getParameter('app.api_url_prod');

        $target_dir = $UPLOAD_DIR;
        if ($appEnv == "dev"){
            $url_dir = $API_URL_DEV;
        } else {
            $url_dir = $API_URL_PROD;
        }

        $photoFile = $request->files->get('photo');

        if (!$photoFile) {
            return $this->json(['message' => 'No photo uploaded']);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($photoFile->getMimeType(), $allowedTypes)) {
            return $this->json(['message' => 'Only JPEG, PNG, and GIF images are allowed']);
        }

        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
        }

        $fileName  = 'userPP_' . $this->randomName() . '.' . $photoFile->guessExtension();

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'token is failed']);
            }

            try {
                $photoFile->move($target_dir, $fileName);
            } catch (\Exception $e) {
                return $this->json(['message' => 'Failed to move the photo']);
            }

            $picture = new Picture();
            $picture->setUrl($url_dir . "/" . $target_dir . $fileName);
            $picture->setUser($user);
            $picture->setPostedAt(new \DateTime());
            $picture->setIp($newIp);

            $this->entityManager->persist($picture);
            $this->entityManager->flush();

            $user->setPp($picture);
            $this->entityManager->persist($user);
            $this->entityManager->flush();


            return $this->json(['message' => 'good', 'result' => $picture], 200, [], ['groups' => 'picture:read']);

        }

        return $this->json(['message' => 'no token']);
    }



    function randomName($length=20){
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        for($i=0; $i<$length; $i++){
            $string .= $chars[rand(0, strlen($chars)-1)];
        }
        return $string;
    }

}

