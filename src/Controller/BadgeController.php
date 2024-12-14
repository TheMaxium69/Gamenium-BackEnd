<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Entity\BadgeVersUser;
use App\Entity\User;
use App\Repository\BadgeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BadgeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BadgeRepository $badgeRepository
    ) {}

    #[Route('/badges', name: 'get_all_badges', methods: ['GET'])]
    public function getAllBadges(): JsonResponse
    {
        $badges = $this->badgeRepository->findAll();

        return $this->json(['result' => $badges, 'message' => 'good'], 200, [], ['groups' => 'badge:read']);
    }

    #[Route('/badge/{id}', name: 'get_badge_by_id', methods: ['GET'])]
    public function getBadgeById(int $id): JsonResponse
    {
        $badge = $this->badgeRepository->find($id);

        if (!$badge) {
            return $this->json(['message' => 'Badge not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($badge);
    }

    #[Route('/badges/user/{id}', name: 'get_badges_by_user', methods: ['GET'])]
    public function getBadgesByUser(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);



        if (!$user){

            return $this->json(['message' => 'user not found'], 200, [], ['groups' => 'badge:read']);

        } else {

            $badgeToUserRepository = $this->entityManager->getRepository(BadgeVersUser::class);
            $badgeToUserEntries = $badgeToUserRepository->findBy(['user' => $user]);

            $badges = [];
            foreach ($badgeToUserEntries as $entry) {
                $badges[] = $entry->getBadge();
            }

            if ($badges == []){

                $message = [
                    'message' => "nada"
                ];

            } else {

                $message = [
                    'message' => "good",
                    'result' => $badges
                ];

            }

            return $this->json($message, 200, [], ['groups' => 'badge:read']);
        }
    }

}
