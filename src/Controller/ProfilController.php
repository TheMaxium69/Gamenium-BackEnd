<?php

namespace App\Controller;

use App\Entity\HistoryMyGame;
use App\Entity\ProfilSocialNetwork;
use App\Entity\User;
use App\Entity\UserRate;
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

            if (in_array('ROLE_BAN', $user->getRoles())) {
                return $this->json(['message' => 'user ban']);
            }

            $profilSocialNetworks = $this->entityManager->getRepository(ProfilSocialNetwork::class)->findBy(['user' => $user]);
            $historyMyGames = $this->entityManager->getRepository(HistoryMyGame::class)->findBy(['user' => $user]);
            $userRates = $this->entityManager->getRepository(UserRate::class)->findBy(['user' => $user]);

            if ($user->getPp() !== null) {
                $picture = $user->getPp()->getUrl();
            } else {
                $picture = null;
            }

            if ($user->getColor() !== null) {
                $color = $user->getColor();
            } else {
                $color = null;
            }

            $message = [
                'message' => "good",
                'result' => [
                    "id" => $user->getId(),
                    "username" => $user->getUsername(),
                    "displayname" => $user->getDisplayname(),
                    "displaynameUseritium" => $user->getDisplaynameUseritium(),
                    "joinAt" => $user->getJoinAt(),
                    "themeColor" => $color,
                    "picture" => $picture,
                    "nbGame" => count($historyMyGames),
                    "nbNote" => count($userRates),
                    "reseau" => $profilSocialNetworks
                ]
            ];

            return $this->json($message, 200, [], ['groups' => 'profilSocialNetwork:read']);
        }
    }







}
