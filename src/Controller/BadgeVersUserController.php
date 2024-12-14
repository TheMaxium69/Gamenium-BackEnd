<?php

namespace App\Controller;

use App\Entity\BadgeVersUser;
use App\Repository\BadgeVersUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BadgeVersUserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BadgeVersUserRepository $badgeVersUserRepository
    ) {}

    #[Route('/badgeversusers', name: 'get_all_badgeversusers', methods: ['GET'])]
    public function getAllBadgeVersUsers(): JsonResponse
    {
        $badgeVersUsers = $this->badgeVersUserRepository->findAll();

        return $this->json($badgeVersUsers , 200 , [], ['groups' => 'badgesversuser:read']);
    }

    #[Route('/badgeversuser/{id}', name: 'get_badgeversuser_by_id', methods: ['GET'])]
    public function getBadgeVersUserById(int $id): JsonResponse
    {
        $badgeVersUser = $this->badgeVersUserRepository->find($id);

        if (!$badgeVersUser) {
            return $this->json(['message' => 'Badge Vers User not found']);
        }

        return $this->json($badgeVersUser);
    }
}
