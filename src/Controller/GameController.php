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

    private $manager;
    private $game;

    public function __construct(EntityManagerInterface $manager, GameRepository $game)
    {
        $this->manager = $manager;
        $this->game = $game;
    }

    #[Route('/games/', name: 'game_all', methods:"GET")]
    public function getGameAll():JsonResponse
    {
        $games = $this->game->findAll();
        if (!$games) {
            return $this->json(['message' => 'Games not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($games, 200, ['groups' => 'game:read']);
    }

    #[Route('/game/{id}', name: 'game_by_id', methods:"GET")]
    public function getGameById(int $id):JsonResponse
    {
        $game = $this->game->find($id);

        if (!$game) {
            return $this->json(['message' => 'Game not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($game);
    }

    #[Route('/game/', name: 'game_create', methods:"POST")]
    public function createGame (Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $game = new Game();
        
        $this->manager->persist($game);
        $this->manager->flush();

        return $this->json(['message' => 'Game created successfully', 'games' => $game]);
    }

    #[Route('/game/{id}', name: 'game_delete', methods:"DELETE")]
    public function deleteGame(int $id):JsonResponse
    {
        $game = $this->game->find($id);

        if (!$game) {
            
            return $this->json(['message' => 'Game not found'], Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($game);
        $this->manager->flush();

        return $this->json(['message' => 'Game deleted successfully']);
    }
}
