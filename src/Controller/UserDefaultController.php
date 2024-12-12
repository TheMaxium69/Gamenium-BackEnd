<?php

namespace App\Controller;

use App\Entity\Devise;
use App\Entity\User;
use App\Entity\UserDefault;
use App\Repository\UserDefaultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserDefaultController extends AbstractController
{
    public function __construct(
        private UserDefaultRepository $userDefaultRepository,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/user-default/', name: 'app_user_default')]
    public function getUserDefault(Request $request): JsonResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'token invalide']);
            }

            $allInfoByUser = $this->userDefaultRepository->findOneBy(['user' => $user]);

            return $this->json(['message' => 'good', 'result' => $allInfoByUser], 200, [], ['groups' => 'default:read']);

        } else {
            return $this->json(['message' => 'no token']);
        }
    }



    /* SETTER */
    #[Route('/user-default/set', name: 'app_user_default_set', methods: ['POST'])]
    public function setUserDefault(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        if(!isset($data['id_devise'])){
            return $this->json(['message' => 'undefine of field']);
        }

        $devise = $this->entityManager->getRepository(Devise::class)->findOneBy(['id' => $data['id_devise']]);
        if(!$devise){
            return $this->json(['message' => 'devise invalide']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'token invalide']);
            }

            $allInfoByUser = $this->userDefaultRepository->findOneBy(['user' => $user]);

            if (!$allInfoByUser) {
                /* Créer une premier fois la table*/
                $allInfoByUser = new UserDefault();
                $allInfoByUser->setUser($user);
            }

            $allInfoByUser->setDevise($devise);
            $this->entityManager->persist($allInfoByUser);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $allInfoByUser], 200, [], ['groups' => 'default:read']);

        } else {
            return $this->json(['message' => 'no token']);
        }




    }




}
