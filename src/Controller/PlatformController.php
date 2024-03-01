<?php

namespace App\Controller;

use App\Entity\Platform;
use App\Repository\PlatformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PlatformController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PlatformRepository $platformRepository
    ) {}

    #[Route('/platforms', name: 'get_all_platforms', methods: ['GET'])]
    public function getPlatformAll(): JsonResponse
    {
        $platforms = $this->platformRepository->findAll();

        return $this->json($platforms , 200 , [], ['groups' => 'platform:read']);
    }

    #[Route('/platform/{id}', name: 'get_platform_by_id', methods: ['GET'])]
    public function getplatformById(int $id): JsonResponse
    {
        $platform = $this->platformRepository->find($id);

        if (!$platform) {
            return $this->json(['message' => 'platform not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($platform);
    }

    #[Route('/platforms', name: 'create_platform', methods: ['POST'])]
    public function createplatform(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $platform = new platform();
        $platform->setName($data['name']);

        $this->entityManager->persist($platform);
        $this->entityManager->flush();

        return $this->json(['message' => 'platform created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/platforms/{id}', name: 'delete_platform', methods: ['DELETE'])]
    public function deleteplatform(int $id): JsonResponse
    {
        $platform = $this->platformRepository->find($id);

        if (!$platform) {
            return $this->json(['message' => 'platform not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($platform);
        $this->entityManager->flush();

        return $this->json(['message' => 'platform deleted successfully']);
    }
}
