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

        return $this->json($gameProfiles , 200 , [], ['groups' => 'gameprofile:read']);
        
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



}