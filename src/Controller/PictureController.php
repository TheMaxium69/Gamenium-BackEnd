<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\User;
use App\Repository\PictureRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $ip = $request->getClientIp();
        if (!isset($ip)) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $ip;
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

    #[Route('/delete/pp/', name: 'delete_photo', methods: ['DELETE'])]
    public function deletePhoto(Request $request) : JsonResponse 
    {   

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);
            
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'token is failed']);
            }

            // On stock l'id de la photo a supprimer
            $pictureId = $user->getPp()->getId();

            // On set null la pp de l'user
            $user->setPp(null);
            
            // On trouve la photo grace a son id et on la supprime de la bdd
            $profilePicture = $this->entityManager->getRepository(Picture::class)->find($pictureId);
            $this->entityManager->remove($profilePicture);
        
            $this->entityManager->flush();
            
            return $this->json(['message' => 'photo supprimée']);
        }

        return $this->json(['message' => 'Erreur dans la suppression de la photo']);
    }

    function randomName($length=20){
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        for($i=0; $i<$length; $i++){
            $string .= $chars[rand(0, strlen($chars)-1)];
        }
        return $string;
    }


    #[Route('/upload/provider', name: 'upload_provider', methods: ['POST'])]
    public function uploadProvider(Request $request): JsonResponse
    {
        $UPLOAD_DIR = $this->getParameter('app.upload_dir');
        $API_URL = str_replace('https://', 'http://', $this->getParameter('app.api_url_dev'));


        if (!$request->files->has('photo')) {
            return $this->json(['message' => 'No photo uploaded'], Response::HTTP_BAD_REQUEST);
        }

        $photoFile = $request->files->get('photo');

        if (!$photoFile || !$photoFile->isValid()) {
            return $this->json(['message' => 'No photo uploaded or invalid file'], Response::HTTP_BAD_REQUEST);
        }



        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($photoFile->getMimeType(), $allowedTypes)) {
            return $this->json(['message' => 'Only JPEG, PNG, and GIF images are allowed'], Response::HTTP_BAD_REQUEST);
        }


        if (!is_dir($UPLOAD_DIR)) {
            mkdir($UPLOAD_DIR, 0777, true);
        }


        $fileName = 'provider_' . uniqid() . '.' . $photoFile->guessExtension();

        try {
            $photoFile->move($UPLOAD_DIR, $fileName);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Failed to save the image', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        $authorizationHeader = $request->headers->get('Authorization');
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            return $this->json(['message' => 'No token provided'], Response::HTTP_UNAUTHORIZED);
        }
        $token = substr($authorizationHeader, 7);

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
        if (!$user) {
            return $this->json(['message' => 'Invalid token'], Response::HTTP_UNAUTHORIZED);
        }

        if (!array_intersect(['ROLE_PROVIDER', 'ROLE_PROVIDER_ADMIN', 'ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_WRITE_RESPONSABLE', 'ROLE_WRITE_SUPER', 'ROLE_WRITE'], $user->getRoles())) {
            return $this->json(['message' => 'no permission']);
        }

        $picture = new Picture();
        $picture->setUrl($API_URL . "/" . $UPLOAD_DIR . "/" . $fileName);
        $picture->setUser($user);
        $picture->setPostedAt(new \DateTime());
        $picture->setIp($request->getClientIp());
        $picture->setIsDeleted(false);

        $this->entityManager->persist($picture);
        $this->entityManager->flush();

        return $this->json(['message' => 'Image uploaded successfully', 'result' => $picture], Response::HTTP_CREATED, [], ['groups' => 'picture:read']);
    }

}

