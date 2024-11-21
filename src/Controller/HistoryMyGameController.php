<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\HistoryMyGame;
use App\Entity\HmgCopy;
use App\Entity\HmgScreenshot;
use App\Entity\HmgSpeedrun;
use App\Entity\User;
use App\Entity\UserRate;
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
                $mygame[] = [
                    "id" => $entry->getId(),
                    "myGame" => $entry,
                    "copyGame" => $this->entityManager->getRepository(HmgCopy::class)->findBy(['HistoryMyGame' => $entry]),
                    "speedrun" => $this->entityManager->getRepository(HmgSpeedrun::class)->findBy(['MyGame' => $entry]),
                    "screenshot" => $this->entityManager->getRepository(HmgScreenshot::class)->findBy(['MyGame' => $entry]),
                    "rate" => $this->entityManager->getRepository(UserRate::class)->findOneBy(['user' => $entry->getUser(), 'game' => $entry->getGame()]),
                ];
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

    #[Route('/OneMyGame/{id}', name: 'get_one_mygame', methods: ['GET'])]
    public function getOneMyGame(int $id): JsonResponse
    {

        $MyGame = $this->historyMyGameRepository->find($id);

        if (!$MyGame){

            return $this->json(['message' => 'my game not found']);

        } else {

//            var_dump($MyGame);

            $copyGame = $this->entityManager->getRepository(HmgCopy::class)->findBy(['HistoryMyGame' => $MyGame]);
            $speedrun = $this->entityManager->getRepository(HmgSpeedrun::class)->findBy(['MyGame' => $MyGame]);
            $screenshot = $this->entityManager->getRepository(HmgScreenshot::class)->findBy(['MyGame' => $MyGame]);
            $rate = $this->entityManager->getRepository(UserRate::class)->findOneBy(['user' => $MyGame->getUser(), 'game' => $MyGame->getGame()]);

            $message = [
                'message' => "good",
                'result' => [
                    "id" => $MyGame->getId(),
                    "myGame" => $MyGame,
                    "copyGame" => $copyGame,
                    "speedrun" => $speedrun,
                    "screenshot" => $screenshot,
                    "rate" => $rate,
                ]
            ];

            return $this->json($message, 200, [], ['groups' => 'historygame:read']);

        }

    }

    #[Route('/addMyGame/', name: 'addMyGame', methods: ['POST'])]
    public function addHistoryMyGame(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id_game']) || !isset($data['is_pinned'])){
            return $this->json(['message' => 'undefine of field']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);


            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            /*SI LE JEUX EXISTE*/
            $game = $this->entityManager->getRepository(Game::class)->findOneBy(['id' => $data['id_game']]);
            if (!$game){
                return $this->json(['message' => 'game is failed']);
            }

            /*SI LE JEUX A DEJA ETE AJOUTER*/
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


    #[Route('/addNoteMyGame/', name: 'addNoteMyGame', methods: ['POST'])]
    public function addNoteMyGame(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id_game']) || !isset($data['note'])){
            return $this->json(['message' => 'undefine of field']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            /*SI LE JEUX EXISTE*/
            $game = $this->entityManager->getRepository(Game::class)->findOneBy(['id' => $data['id_game']]);
            if (!$game){
                return $this->json(['message' => 'game is failed']);
            }

            /*SI LA NOTE EST AU DESSUS DE 20*/
            if ($data['note'] > 20){
                return $this->json(['message' => 'note no valide']);
            }

            /*SI LE JEUX EST BIEN DANS SA COLLECTION*/
            $MyGameSelectedToUser = $this->historyMyGameRepository->findOneBy(['user' => $user, 'game' => $game]);
            if (!$MyGameSelectedToUser){
                return $this->json(['message' => 'game not in collection']);
            }

            /*SI LA NOTE A DEJA ETE DONNER*/
            $MyNoteGameSelectedToUser = $this->entityManager->getRepository(UserRate::class)->findOneBy(['user' => $user, 'game' => $game]);
            if ($MyNoteGameSelectedToUser){
                return $this->json(['message' => 'note existing']);
            }

            $userNote = new UserRate();
            $userNote->setUser($user);
            $userNote->setGame($game);
            $userNote->setRating($data['note']);
            $userNote->setCreatedAt(New \DateTimeImmutable());
            $userNote->setIp("10.10.10.10");

            $this->entityManager->persist($userNote);
            $this->entityManager->flush();

            return $this->json(['message' => 'add note is game', 'result' => $userNote], 200, [], ['groups' => 'userRate:read']);
        }

        return $this->json(['message' => 'no token']);
    }

    #[Route('/updateMyGame/', name: 'updateMyGame', methods: ['PUT'])]
    public function updateHistoryMyGame(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Format JSON invalide']);
        }

        /*SI LES CHAMP SON REMPLIE */
//        if (!isset($data['id_game']) || !isset($data['is_pinned'])) {
//            return $this->json(['message' => 'Champs requis manquants']);
//        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /* SI L'UTILISATEUR CORRESPOND AU TOKEN  */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'Token invalide']);
            }

            /*SI LE HISTOIRE MY GAME CORRESPOND */
            $historyMyGame = $this->entityManager->getRepository(HistoryMyGame::class)->findOneBy(['id' => $data['myGame']['id']]);
            if (!$historyMyGame) {
                return $this->json(['message' => 'Jeu introuvable']);
            } else if ($historyMyGame->getUser() == $user) {

                /* FAIRE LA MODIF*/
                if (isset($data['myGame']['is_pinned']) && $data['myGame']['is_pinned'] != $historyMyGame->isIsPinned() && $data['myGame']['is_pinned'] || !$data['myGame']['is_pinned']){
                    $historyMyGame->setIsPinned($data['myGame']['is_pinned']);
                }
                if (isset($data['myGame']['difficulty_rating']) && $data['myGame']['difficulty_rating'] != $historyMyGame->getDifficultyRating() && $data['myGame']['difficulty_rating'] >= 1 && $data['myGame']['difficulty_rating'] <= 5) {
                    $historyMyGame->setDifficultyRating($data['myGame']['difficulty_rating']);
                }
                if (isset($data['myGame']['lifetime_rating']) && $data['myGame']['lifetime_rating'] != $historyMyGame->getLifetimeRating() && $data['myGame']['lifetime_rating'] >= 1 && $data['myGame']['lifetime_rating'] <= 5) {
                    $historyMyGame->setLifetimeRating($data['myGame']['lifetime_rating']);
                }
                if (isset($data['myGame']['wish_list']) && $data['myGame']['wish_list'] != $historyMyGame->isWishList() && $data['myGame']['wish_list'] || !$data['myGame']['wish_list']){
                    $historyMyGame->setWishList($data['myGame']['wish_list']);
                }

                $this->entityManager->persist($historyMyGame);
                $this->entityManager->flush();


            }

            /*SI LE JEU EST DANS LA COLLECTION */
//            $historyMyGame = $this->historyMyGameRepository->findOneBy(['user' => $user, 'game' => $game]);
//            if (!$historyMyGame) {
//                return $this->json(['message' => 'Le jeu n\'est pas dans votre collection']);
//            }
//
//            /* METTRE A JOUR LE STATUT PINNED */


            /* FORMER LE RETOUR*/
            $message = [
                'message' => "updated game",
                'result' => [
                    "id" => $historyMyGame->getId(),
                    "myGame" => $historyMyGame,
                    "copyGame" => $data['copyGame'],
                    "speedrun" => $data['speedrun'],
                    "screenshot" => $data['screenshot'],
                    "rate" => $data['rate'],
                ]
            ];


            return $this->json($message, 200, [], ['groups' => 'historygame:read']);
        }

        return $this->json(['message' => 'Token manquant']);
    }

    #[Route('/updatePinMyGame/', name: 'updatePinMyGame', methods: ['PUT'])]
    public function updatePinHistoryMyGame(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Format JSON invalide']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id_game']) || !isset($data['is_pinned'])) {
            return $this->json(['message' => 'Champs requis manquants']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /* SI L'UTILISATEUR CORRESPOND AU TOKEN  */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'Token invalide']);
            }

            /*SI LE JEU CORRESPOND */
            $game = $this->entityManager->getRepository(Game::class)->findOneBy(['id' => $data['id_game']]);
            if (!$game) {
                return $this->json(['message' => 'Jeu introuvable']);
            }

            /*SI LE JEU EST DANS LA COLLECTION */
            $historyMyGame = $this->historyMyGameRepository->findOneBy(['user' => $user, 'game' => $game]);
            if (!$historyMyGame) {
                return $this->json(['message' => 'Le jeu n\'est pas dans votre collection']);
            }

            /* METTRE A JOUR LE STATUT PINNED */
            $historyMyGame->setIsPinned($data['is_pinned']);

            $this->entityManager->persist($historyMyGame);
            $this->entityManager->flush();

            return $this->json(['message' => 'game is pinned', 'result' => $data], 200, [], ['groups' => 'historygame:read']);
        }

        return $this->json(['message' => 'Token manquant']);
    }


}
