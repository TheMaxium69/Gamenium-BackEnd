<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\HistoryMyGame;
use App\Entity\User;
use App\Repository\HistoryMyGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class HistoryMyGameController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HistoryMyGameRepository $historyMyGameRepository
    ) {}

    #[Route('/MyGameByUser/{id}', name: 'get_mygame_by_user', methods: ['GET'])]
    public function getMyGameByUser(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user){

            return $this->json(['message' => 'user not found']);

        } else {

            $MyUserToUserEntries = $this->historyMyGameRepository->findBy(['user' => $user]);

            $mygame = [];
            foreach ($MyUserToUserEntries as $entry) {
                $mygame[] = $entry;
            }

            if ($mygame == []){

                $message = [
                    'message' => "aucun jeux"
                ];

            } else {

                $message = [
                    'message' => "good",
                    'result' => $mygame
                ];

            }

            return $this->json($message, 200, [], ['groups' => 'historygame:read']);
        }
    }


//    #[Route('/historymygame/{id}', name: 'get_historymygame_by_id', methods: ['GET'])]
//    public function getHistoryMyGameById(int $id): JsonResponse
//    {
//        $historyMyGame = $this->historyMyGameRepository->find($id);
//
//        if (!$historyMyGame) {
//            return $this->json(['message' => 'HistoryMyGame not found'], Response::HTTP_NOT_FOUND);
//        }
//
//        return $this->json($historyMyGame);
//    }

    #[Route('/mygame/', name: 'create_historymygame', methods: ['POST'])]
    public function createHistoryMyGame(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        if (!isset($data['id_game']) || !isset($data['is_pinned'])){
            return $this->json(['message' => 'undefine of field']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }
            $game = $this->entityManager->getRepository(Game::class)->findOneBy(['id' => $data['id_game']]);
            if (!$game){
                return $this->json(['message' => 'game is failed']);
            }

            $MyGameSelectedToUser = $this->historyMyGameRepository->findOneBy(['user' => $user, 'game' => $game]);
            if ($MyGameSelectedToUser){
                return $this->json(['message' => 'has already been added']);
            }

            $historyMyGame = new HistoryMyGame();
            $historyMyGame->setUser($user);
            $historyMyGame->setGame($game);
            $historyMyGame->setIsPinned($data['is_pinned']);
            $historyMyGame->setAddedAt(New \DateTimeImmutable());

            $this->entityManager->persist($historyMyGame);
            $this->entityManager->flush();

            return $this->json(['message' => 'add game is collection', 'result' => $historyMyGame], 200, [], ['groups' => 'historygame:read']);
        }

        return $this->json(['message' => 'no token']);
    }

//    #[Route('/historymygame/{id}', name: 'delete_historymygame', methods: ['DELETE'])]
//    public function deleteHistoryMyGame(int $id): JsonResponse
//    {
//        $historyMyGame = $this->historyMyGameRepository->find($id);
//
//        if (!$historyMyGame) {
//            return $this->json(['message' => 'HistoryMyGame not found'], Response::HTTP_NOT_FOUND);
//        }
//
//        $this->entityManager->remove($historyMyGame);
//        $this->entityManager->flush();
//
//        return $this->json(['message' => 'HistoryMyGame deleted successfully']);
//    }
}
