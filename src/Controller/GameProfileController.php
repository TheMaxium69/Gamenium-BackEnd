<?php

namespace App\Controller;

use App\Entity\GameProfile;
use App\Repository\GameProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class GameProfileController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GameProfileRepository $gameProfileRepository
    ) {}

    #[Route('/gameprofiles', name: 'get_all_gameprofiles', methods: ['GET'])]
    public function getAllGameProfiles(): JsonResponse
    {
        $gameProfiles = $this->gameProfileRepository->findAll();

        if (!$gameProfiles) {
            return $this->json(['message' => 'GameProfiles not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($gameProfiles , 200);
        
    }

    #[Route('/gameprofile/{id}', name: 'get_gameprofile_by_id', methods: ['GET'])]
    public function getGameProfileById(int $id): JsonResponse
    {
        $gameProfile = $this->gameProfileRepository->find($id);

        if (!$gameProfile) {
            return $this->json(['message' => 'GameProfile not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($gameProfile);
    }
#[Route('/gameprofiles', name: 'create_gameprofile', methods: ['POST'])]
    public function createGameProfile(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $gameProfile = new GameProfile();
        $gameProfile->setJoinedAt(new \DateTimeImmutable($data['joined_at']));
        $picture = new Picture();
        $picture->setUrl($data['picture']['url']);
        $picture->setPostedAt(new \DateTimeImmutable($data['picture']['posted_at']));
        $picture->setIp($data['picture']['ip']);

        $gameProfile->setPicture($picture);

        $this->entityManager->persist($gameProfile);
        $this->entityManager->flush();

        return $this->json(['message' => 'GameProfile created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/gameprofiles/{id}', name: 'delete_gameprofile', methods: ['DELETE'])]
    public function deleteGameProfile(int $id): JsonResponse
    {
        $gameProfile = $this->gameProfileRepository->find($id);

        if (!$gameProfile) {
            return $this->json(['message' => 'GameProfile not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($gameProfile);
        $this->entityManager->flush();

        return $this->json(['message' => 'GameProfile deleted successfully']);
    }
}