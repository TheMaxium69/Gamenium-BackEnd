<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GameRepository $gameRepository
    ) {}

    #[Route('/games', name: 'get_all_games', methods: ['GET'])]
    public function getAllGames(): JsonResponse
    {
        $games = $this->gameRepository->findAll();

        return $this->json($games , 200 , [], ['groups' => 'game:read']);
    }

    #[Route('/games/{page}/{limit}', name: 'get_all_games_paginated', methods: ['GET'])]
public function getAllGamesPaginated(Request $request, int $page, int $limit, GameRepository $gameRepository): JsonResponse
{

    $offset = ($page - 1) * $limit;

    $games = $gameRepository->findBy([], null, $limit, $offset);

    return $this->json($games , 200 , [], ['groups' => 'game:read']);
}

    #[Route('/game/{id}', name: 'get_game_by_id', methods: ['GET'])]
    public function getGameById(int $id): JsonResponse
    {
        $game = $this->gameRepository->find($id);

        if (!$game) {
            return $this->json(['message' => 'Game not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($game);
    }

    #[Route('/game', name: 'create_game', methods: ['POST'])]
    public function createGame(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $game = new Game();

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return $this->json(['message' => 'Game created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/game/{id}', name: 'delete_game', methods: ['DELETE'])]
    public function deleteGame(int $id): JsonResponse
    {
        $game = $this->gameRepository->find($id);

        if (!$game) {
            return $this->json(['message' => 'Game not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($game);
        $this->entityManager->flush();

        return $this->json(['message' => 'Game deleted successfully']);
    }
}
