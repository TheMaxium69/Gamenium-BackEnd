<?php

namespace App\Controller;

use App\Entity\HistoryMyGame;
use App\Repository\HistoryMyGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryMyGameController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HistoryMyGameRepository $historyMyGameRepository
    ) {}

    #[Route('/historymygames', name: 'get_all_historymygames', methods: ['GET'])]
    public function getAllHistoryMyGames(): JsonResponse
    {
        $historyMyGames = $this->historyMyGameRepository->findAll();

        return $this->json($historyMyGames);
    }

    #[Route('/historymygame/{id}', name: 'get_historymygame_by_id', methods: ['GET'])]
    public function getHistoryMyGameById(int $id): JsonResponse
    {
        $historyMyGame = $this->historyMyGameRepository->find($id);

        if (!$historyMyGame) {
            return $this->json(['message' => 'HistoryMyGame not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($historyMyGame);
    }

    #[Route('/historymygame', name: 'create_historymygame', methods: ['POST'])]
    public function createHistoryMyGame(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $historyMyGame = new HistoryMyGame();
        $historyMyGame->setIdUser($data['id_user']);
        $historyMyGame->setIdGame($data['id_game']);
        $historyMyGame->setIsFavorite($data['is_favorite']);
        $historyMyGame->setIdNoteUser($data['id_note_user']);
        $historyMyGame->setContent($data['content']);
        $historyMyGame->setBuyAt(new \DateTimeImmutable($data['buy_at']));
        $historyMyGame->setIdBuyWhere($data['id_buy_where']);

        $this->entityManager->persist($historyMyGame);
        $this->entityManager->flush();

        return $this->json(['message' => 'HistoryMyGame created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/historymygame/{id}', name: 'delete_historymygame', methods: ['DELETE'])]
    public function deleteHistoryMyGame(int $id): JsonResponse
    {
        $historyMyGame = $this->historyMyGameRepository->find($id);

        if (!$historyMyGame) {
            return $this->json(['message' => 'HistoryMyGame not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($historyMyGame);
        $this->entityManager->flush();

        return $this->json(['message' => 'HistoryMyGame deleted successfully']);
    }
}
