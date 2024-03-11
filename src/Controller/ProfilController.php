<?php

namespace App\Controller;

use App\Entity\ProfilSocialNetwork;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/profil/{id}', name: 'get_profil_user_id')]
    public function getProfilByUser(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user){

            return $this->json(['message' => 'user not found']);

        } else {

            $profilSocialNetworks = $this->entityManager->getRepository(ProfilSocialNetwork::class)->findBy(['user' => $user]);

            $message = [
                'message' => "good",
                'result' => [
                    "id" => $user->getId(),
                    "username" => $user->getUsername(),
                    "displayname" => $user->getDisplayname(),
                    "displaynameUseritium" => $user->getDisplaynameUseritium(),
                    "joinAt" => $user->getJoinAt(),
                    "themeColor" => $user->getColor(),
                    "picture" => $user->getPp()->getUrl(),
                    "nbGame" => 10,
                    "nbNote" => 5,
                    "reseau" => $profilSocialNetworks
                ]
            ];

            return $this->json($message, 200, [], ['groups' => 'profilSocialNetwork:read']);
        }
    }







}
