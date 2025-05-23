<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Test;
use App\Entity\UserRate;
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

//    #[Route('/games', name: 'get_all_games', methods: ['GET'])]
//    public function getAllGames(): JsonResponse
//    {
//        $games = $this->gameRepository->findAll();
//
//        return $this->json($games , 200 , [], ['groups' => 'game:read']);
//    }

//    #[Route('/games/{page}/{limit}', name: 'get_all_games_paginated', methods: ['GET'])]
//    public function getAllGamesPaginated(Request $request, int $page, int $limit, GameRepository $gameRepository): JsonResponse
//    {
//
//        if ($limit <= 100){
//
//            $offset = ($page - 1) * $limit;
//
//            $games = $gameRepository->findBy([], null, $limit, $offset);
//
//            if ($games == []) {
//                $message = [
//                    'message' => "vide",
//                    'page' => $page,
//                    'limit' => $limit,
//                ];
//                return $this->json($message);
//            } else {
//                $message = [
//                    'message' => "good",
//                    'page' => $page,
//                    'limit' => $limit,
//                    'result' => $games
//                ];
//
//                return $this->json($message , 200 , [], ['groups' => 'game:read']);
//            }
//
//        } else {
//
//            $message = [
//                'message' => "100 is max",
//                'page' => $page,
//                'limit' => $limit,
//            ];
//            return $this->json($message);
//        }
//
//    }

    #[Route('/game/{id}', name: 'get_game_by_id', methods: ['GET'])]
    public function getGameById(int $id): JsonResponse
    {
        $game = $this->gameRepository->find($id);

        if(!$game){
            return $this->json(['message' => 'Game not found']);
        } else {

            $testGame = $this->entityManager->getRepository(Test::class)->findBy(['game' => $game]);

            $game->setMoyenRateUser($this->entityManager->getRepository(UserRate::class)->calcMoyenByGame($game->getId()));
            $message = [
                'message' => "good",
                'result' => $game,
                'result2' => $testGame
            ];
        }

        return $this->json($message, 200 , [], ['groups' => 'game:read']);
    }


    #[Route('/latest-games', name:'latest_games', methods: ['POST'])]
    public function getLatestGames(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $limit = $data['limit'];

        $results = $this->gameRepository->latestGames($limit);

        if ($results) {
            $message = [
                'message' => "good",
                'result' => $results
            ];
        } else {
            $message = [
                'message' => 'no game'
            ];
        }

        return $this->json($message, 200, [], ['groups' => 'game:read']);
        
    }

//    #[Route('/game', name: 'create_game', methods: ['POST'])]
//    public function createGame(Request $request): JsonResponse
//    {
//        $data = json_decode($request->getContent(), true);
//
//        $game = new Game();
//
//        $this->entityManager->persist($game);
//        $this->entityManager->flush();
//
//        return $this->json(['message' => 'Game created successfully'], Response::HTTP_CREATED);
//    }

//    #[Route('/game/{id}', name: 'delete_game', methods: ['DELETE'])]
//    public function deleteGame(int $id): JsonResponse
//    {
//        $game = $this->gameRepository->find($id);
//
//        if (!$game) {
//            return $this->json(['message' => 'Game not found']);
//        }
//
//        $this->entityManager->remove($game);
//        $this->entityManager->flush();
//
//        return $this->json(['message' => 'Game deleted successfully']);
//    }


    #[Route('/games/search', name: 'search_games', methods: ['POST'])]
    public function searchGames(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $searchValue = $data['searchValue'] ?? '';
        $limit = $data['limit'];

        $results = $this->gameRepository->searchByName($searchValue, $limit);

        $finalResults = [];
        foreach($results as $oneGame){

            /* JSON */
            $oneGame['image'] = json_decode($oneGame['image']);
            $oneGame['imageTags'] = json_decode($oneGame['image_tags']);
            $oneGame['originalGameRating'] = json_decode($oneGame['original_game_rating']);
            $oneGame['platforms'] = json_decode($oneGame['platforms']);
            $oneGame['moyenRateUser'] = $this->entityManager->getRepository(UserRate::class)->calcMoyenByGame($oneGame['id']);

            /* nameVariable */
            $oneGame = array_merge($oneGame, [
                'dateLastUpdated' => $oneGame['date_last_updated'],
                'expectedReleaseDay' => $oneGame['expected_release_day'],
                'expectedReleaseMonth' => $oneGame['expected_release_month'],
                'expectedReleaseYear' => $oneGame['expected_release_year'],
                'id_GiantBomb' => $oneGame['id_giant_bomb'],
                'siteDetailUrl' => $oneGame['site_detail_url'],
                'originalReleaseDate' => $oneGame['original_release_date'],
                'numberOfUserReviews' => $oneGame['number_of_user_reviews'],
            ]);




            $finalResults[] = $oneGame;
        }
    
        return $this->json($finalResults, 200, []);
    }


}
