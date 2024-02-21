<?php


namespace App\Controller;

use App\Entity\Badge;
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

        return $this->json($badges);
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

    #[Route('/badge', name: 'create_badge', methods: ['POST'])]
    public function createBadge(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $badge = new Badge();
        $badge->setName($data['name']);
        $badge->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($badge);
        $this->entityManager->flush();

        return $this->json(['message' => 'Badge created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/badge/{id}', name: 'delete_badge', methods: ['DELETE'])]
    public function deleteBadge(int $id): JsonResponse
    {
        $badge = $this->badgeRepository->find($id);

        if (!$badge) {
            return $this->json(['message' => 'Badge not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($badge);
        $this->entityManager->flush();

        return $this->json(['message' => 'Badge deleted successfully']);
    }
}

