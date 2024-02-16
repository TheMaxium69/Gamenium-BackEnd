<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/games/', name: 'game_all', methods:"GET")]
    public function getGameAll(Game $game):JsonResponse
    {
        return $this->json(['id' => $game->getId()]);
    }
    #[Route('/game/{id}', name: 'game_by_id', methods:"GET")]
    public function getGameById(Game $game):JsonResponse
    {
        return $this->json(['id' => $game->getId()]);

    }
}
