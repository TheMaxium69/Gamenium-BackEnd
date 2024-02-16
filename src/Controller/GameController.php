<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/games/', name: 'game_all', methods:"GET")]
    public function getGameAll():JsonResponse
    {
        $games = $this->getDoctrine()->getRepository(Game::class)->findAll();
        $data = [];
        foreach ($games as $game){
            $data[] = ['id' => $game->getId()];
        }
        return $this->json($data);
    }

    #[Route('/game/{id}', name: 'game_by_id', methods:"GET")]
    public function getGameById(Game $game):JsonResponse
    {
        return $this->json(['id' => $game->getId()]);
    }

    #[Route('/game/', name: 'game_create', methods:"POST")]
    public function createGame (Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $game = new Game();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($game);
        $entityManager->flush();

        return $this->json(['message' => 'Game created successfully', 'id' => $game->getId()]);
    }

    #[Route('/game/{id}', name: 'game_delete', methods:"DELETE")]
    public function deleteGame(Game $game):JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($game);
        $entityManager->flush();

        return $this->json(['message' => 'Game deleted successfully']);
    }
}
