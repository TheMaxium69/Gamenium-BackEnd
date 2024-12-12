<?php

namespace App\Controller;

use App\Entity\HistoryMyGame;
use App\Entity\HmgScreenshot;
use App\Entity\HmgScreenshotCategory;
use App\Entity\Picture;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HmgScreenshotController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/upload/screenshot/', name: 'app_hmg_screenshot', methods:['POST'])]
    public function upload(Request $request): JsonResponse
    {

        $data = [
            'id_mygame' => $request->request->get('id_mygame', null),
            'id_category' => $request->request->get('id_category', null),
            'ip' => $request->request->get('ip', '0.0.0.0')
        ];

        if(!isset($data['id_mygame']) && !isset($data['id_category'])){
            return $this->json(['message' => 'undefine of field']);
        }

        if (!isset($data['ip'])){
            $newIp = $data['ip'];
        } else {
            $newIp = "0.0.0.0";
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /* SI L'UTILISATEUR CORRESPOND AU TOKEN  */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'token invalide']);
            }

            /*SI LE HISTOIRE MY GAME CORRESPOND */
            $historyMyGame = $this->entityManager->getRepository(HistoryMyGame::class)->findOneBy(['id' => $data['id_mygame']]);
            if (!$historyMyGame) {
                return $this->json(['message' => 'Jeu introuvable']);
            }

            /* VERIF SI LE MY GAME APARTIEN BIEN A USER*/
            if ($user->getId() !== $historyMyGame->getUser()->getId()) {
                return $this->json(['message' => 'no permission']);
            }

            /* VERIF LA CATEGORY */
            $category = $this->entityManager->getRepository(HmgScreenshotCategory::class)->findOneBy(['id' => $data['id_category']]);
            if (!$category) {
                return $this->json(['message' => 'category not found']);
            }

            /* GEREZ L'UPLOAD */

            $appEnv = $this->getParameter('app.app_env');
            $UPLOAD_DIR = $this->getParameter('app.upload_dir');
            $API_URL_DEV = $this->getParameter('app.api_url_dev');
            $API_URL_PROD = $this->getParameter('app.api_url_prod');

            if ($appEnv === 'dev') {
                $API_URL = $API_URL_DEV;
            } else {
                $API_URL = $API_URL_PROD;
            }

            $screenshot = $request->files->get('picture');

            if (!$screenshot) {
                return $this->json(['message' => 'file is missing']);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];

            if (!in_array($screenshot->getMimeType(), $allowedTypes)){
                return $this->json(['message' => 'not allowed type']);
            }


            $fileName = 'screenshotGame_' . $this->randomName() . '.' . $screenshot->guessExtension();

            try {

                $screenshot->move($UPLOAD_DIR, $fileName);

            } catch (\Exception $e) {
                return $this->json(['message' => 'failed to move']);
            }

            $picture = new Picture();

            $picture->setUrl($API_URL . "/" . $UPLOAD_DIR . $fileName);
            $picture->setIp($newIp);
            $picture->setUser($user);
            $picture->setPostedAt(new \DateTime());

            $this->entityManager->persist($picture);
            $this->entityManager->flush();

            $screenshot = new HmgScreenshot();
            $screenshot->setPicture($picture);
            $screenshot->setCategory($category);
            $screenshot->setMyGame($historyMyGame);

            $this->entityManager->persist($screenshot);
            $this->entityManager->flush();


            return $this->json(['message' => 'good', 'result' => $screenshot], 200, [], ['groups' => 'screenshot:read']);

        } else {
            return $this->json(['message' => 'no token']);
        }


    }

    #[Route('/delete-screenshot/{id}', name: 'delete_photo', methods: ['DELETE'])]
    public function deletePhoto(int $id, Request $request) : JsonResponse 
    {   

        if(!$id){
            return $this->json(['message' => 'no screenshot found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);
            
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'token is failed']);
            }

            // On stock l'id de la photo a supprimer
            $screenshot = $this->entityManager->getRepository(HmgScreenshot::class)->findBy() 

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
}

