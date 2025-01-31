<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Test;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('test')]
class TestController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}


    #[Route('/getbygame/{id}', name: 'test_by_game')]
    public function getTestByGame(int $id): JsonResponse
    {


        $game = $this->entityManager->getRepository(Game::class)->find($id);

        if (!$game) {
            return $this->json(['message' => 'game not found']);
        }


        $tests = $this->entityManager->getRepository(Test::class)->findBy(['game' => $game]);

        if (!$tests) {
            return $this->json(['message' => 'no tests found for this game']);
        }

        return $this->json(['message'=>'good','result' => $tests], 200, [], ['groups' => 'testRate:read']);
    }
    
    
    
}
