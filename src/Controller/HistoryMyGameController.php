<?php

namespace App\Controller;

use App\Entity\BuyWhere;
use App\Entity\Devise;
use App\Entity\Game;
use App\Entity\HistoryMyGame;
use App\Entity\HmgCopy;
use App\Entity\HmgCopyEtat;
use App\Entity\HmgCopyFormat;
use App\Entity\HmgCopyLanguage;
use App\Entity\HmgCopyPurchase;
use App\Entity\HmgCopyRegion;
use App\Entity\HmgScreenshot;
use App\Entity\HmgSpeedrun;
use App\Entity\HmgTags;
use App\Entity\Plateform;
use App\Entity\User;
use App\Entity\UserRate;
use App\Entity\Warn;
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

    #[Route('/MyGameByUserWithPlateforme/{id_user}/{id_plateforme_gb}', name: 'get_mygame_by_user_with_plateform', methods: ['GET'])]
    public function getMyGameByUserWithPlateforme(int $id_user, int $id_plateforme_gb): JsonResponse
    {

        $user = $this->entityManager->getRepository(User::class)->find($id_user);
        if (!$user){
            return $this->json(['message' => 'user not found']);
        }

        $plateform = $this->entityManager->getRepository(Plateform::class)->find($id_plateforme_gb);
        if (!$plateform){
            return $this->json(['message' => 'plateform not found']);
        }


        $MyUserToUserEntries = $this->historyMyGameRepository->findBy(['user' => $user, 'plateform' => $plateform]);

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
        if (!isset($data['id_game']) || !isset($data['id_plateform'])){
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

            $plateform = $this->entityManager->getRepository(Plateform::class)->findOneBy(['id_giant_bomb' => $data['id_plateform']]);
            if (!$plateform){
                return $this->json(['message' => 'plateform is failed']);
            }

            /*SI LE JEUX A DEJA ETE AJOUTER*/
            $MyGameSelectedToUser = $this->historyMyGameRepository->findOneBy(['user' => $user, 'game' => $game, 'plateform' => $plateform]);
            if ($MyGameSelectedToUser){
                return $this->json(['message' => 'has already been added']);
            }

            $historyMyGame = new HistoryMyGame();
            $historyMyGame->setUser($user);
            $historyMyGame->setGame($game);
            $historyMyGame->setPlateform($plateform);
            $historyMyGame->setAddedAt(new \DateTimeImmutable());
            $historyMyGame->setIsPinned(isset($data['is_pinned']) ? $data['is_pinned'] : false);
            $historyMyGame->setWishList(isset($data['is_wishlist']) ? $data['is_wishlist'] : false);
            $this->entityManager->persist($historyMyGame);

            /* GERE LE PURCHASE */
            $newPurchase = new HmgCopyPurchase();
            $newPurchase->setYearBuyAt(isset($data['year_buy_at']) ? $data['year_buy_at'] : null);
            $newPurchase->setMonthBuyAt(isset($data['month_buy_at']) ? $data['month_buy_at'] : null);
            $newPurchase->setDayBuyAt(isset($data['day_buy_at']) ? $data['day_buy_at'] : null);
            if (!empty($data['buywhere_id'])) {
                if ($data['buywhere_id'] && $data['buywhere_id'] != "" && $data['buywhere_id'] != null) {
                    $newBuyWhere = $this->entityManager->getRepository(BuyWhere::class)->findOneBy(['id' => $data['buywhere_id']]);
                    if ($newBuyWhere) {
                        $newPurchase->setBuyWhere($newBuyWhere);
                    }
                }
            }
            $this->entityManager->persist($newPurchase);

            /* GERE LA COPY */
            $newCopy = new HmgCopy();
            $newCopy->setHistoryMyGame($historyMyGame);
            $newCopy->setPurchase($newPurchase);
            $this->entityManager->persist($newCopy);

            $this->entityManager->flush();


            /* GET MY GAME*/
            $rate = $this->entityManager->getRepository(UserRate::class)->findOneBy(['user' => $user, 'game' => $game]);
            $result = [
                "id" => $historyMyGame->getId(),
                "myGame" => $historyMyGame,
                "copyGame" => [$newCopy],
                "speedrun" => [],
                "screenshot" => [],
                "rate" => $rate,
            ];

            return $this->json(['message' => 'add game is collection', 'result' => $result], 200, [], ['groups' => 'historygame:read']);
        }

        return $this->json(['message' => 'no token']);
    }

    #[Route('/deleteMyGame/{id}', name:'deleteMyGame', methods:['DELETE'])]
    public function deleteMyGame(Request $request, int $id) : JsonResponse 
    {

        if (!$id) {
            return $this->json(['message' => 'id is required']);
        }

        $myGame = $this->entityManager->getRepository(HistoryMyGame::class)->findOneBy(['id' => $id]);
        if (!$myGame) {
            return $this->json(['message' => 'Jeu introuvable']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            // Verifi si il est bien a lui
            if ($myGame->getUser() != $user){
                return $this->json(['message' => 'no permission']);
            }

            
            // Trouve l'entrée dans hmg_copy correspondante
            $myGameCopy = $this->entityManager->getRepository(HmgCopy::class)->findBy(['HistoryMyGame' => $myGame]);

            /* SI YA PAS DE COPY C PAS GRAVE*/
            if ($myGameCopy) {
                foreach ($myGameCopy as $oneMyGameCopy) {
                    $this->entityManager->remove($oneMyGameCopy);
                }
            }

            // Check si l'user a ajouté une note pour ce jeux et la supprime s'il y a un résultat
            $game = $myGame->getGame();
            if (count($this->entityManager->getRepository(HistoryMyGame::class)->findBy(['game' => $game, 'user' => $user])) == 1) {
                $userRate = $this->entityManager->getRepository(UserRate::class)->findOneBy(['game' => $game, 'user' => $user]);
    
                if ($userRate) {
                    $this->entityManager->remove($userRate);
                }
            }

            $warnAll = $this->entityManager->getRepository(Warn::class)->findBy(['hmg' => $myGame]);
            if($warnAll){
                foreach ($warnAll as $oneWarn) {
                    $this->entityManager->remove($oneWarn);
                }
            }

            $this->entityManager->remove($myGame);
            $this->entityManager->flush();

            return $this->json(['message' => 'delete success']);
            
        }

        return $this->json(['message' => 'Token manquant']);
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

        $ip = $request->getClientIp();
        if (!isset($ip)) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $ip;
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
            if (filter_var($data['note'], FILTER_VALIDATE_INT) !== false && $data['note'] >= 0 && $data['note'] <= 20) {

                $noteValide = $data['note'];

            } else {
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

                if ($MyNoteGameSelectedToUser->getRating() != $noteValide){
                    $MyNoteGameSelectedToUser->setRating($noteValide);
                }

                if (!empty($data['content'])){
                    $MyNoteGameSelectedToUser->setContent($data['content']);
                }

                $this->entityManager->persist($MyNoteGameSelectedToUser);
                $this->entityManager->flush();

                return $this->json(['message' => 'add note is game', 'result' => $MyNoteGameSelectedToUser], 200, [], ['groups' => 'userRate:read']);
            }

            $userNote = new UserRate();
            $userNote->setUser($user);
            $userNote->setGame($game);
            $userNote->setRating($noteValide);
            if (!empty($data['content'])){
                if ($data['content'] != null && $data['content'] != ""){
                    $userNote->setContent($data['content']);
                }
            }
            $userNote->setCreatedAt(New \DateTimeImmutable());
            $userNote->setIp($newIp);

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
            }

            /* VERIFIER QUE LUSER EST BIEN LE PROPRIO */
            if ($historyMyGame->getUser() == $user) {

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


                /* GESTION DES TAGS*/
                if (isset($data['myGame']['hmgTags'])){

                    $allTags = $data['myGame']['hmgTags'];
                    $tagsDB = $historyMyGame->getHmgTags();

                        /* AJOUTER LES NOUVEAU*/

                    /* ADD */
                    foreach ($allTags as $tagOne) {
                        $found = false;
                        foreach ($tagsDB as $tagOneDB) {
                            if ($tagOneDB->getId() == $tagOne) {
                                $found = true;
                                break;
                            }
                        }

                        if (!$found) {
                            $tagToAdd = $this->entityManager->getRepository(HmgTags::class)->findOneBy(['id' => $tagOne]);
                            $historyMyGame->addHmgTag($tagToAdd);
                        }

                    }


                    /* SUPRESSION */

                    foreach ($tagsDB as $tagOneDB){
                        $found = false;
                        foreach ($allTags AS $tagOne) {
                            if ($tagOne == $tagOneDB->getId()) {
                                $found = true;
                                break;
                            }
                        }

                        if (!$found){
                            $historyMyGame->removeHmgTag($tagOneDB);
                        }
                    }

                }

                $this->entityManager->persist($historyMyGame);
                $this->entityManager->flush();


                /*RECUPERE LES COPY DE JEUX */
                $copyGameAll = $this->entityManager->getRepository(HmgCopy::class)->findBy(['HistoryMyGame' => $historyMyGame]);

                $updatedCopyGameAll = $data['copyGame'];

                $tempAddCopy = [];
                $finalCopyGame = [];

                /* UPDATE LES COPY EXISTANT */
                foreach ($updatedCopyGameAll as $updatedCopyGameOne) {

                    $found = false;

                    foreach ($copyGameAll as $copyGameOne) {

                        if ($copyGameOne->getId() === $updatedCopyGameOne['id']) {
                            /* EDIT SA */

                            if ($copyGameOne->getEdition() != $updatedCopyGameOne['edition']){
                                $copyGameOne->setEdition($updatedCopyGameOne['edition']);
                            }
                            if ($copyGameOne->getBarcode() != $updatedCopyGameOne['barcode']){
                                $copyGameOne->setBarcode($updatedCopyGameOne['barcode']);
                            }
                            if ($copyGameOne->getContent() != $updatedCopyGameOne['content']){
                                $copyGameOne->setContent($updatedCopyGameOne['content']);
                            }

                            if ($copyGameOne->getEtat()){
                                if ($copyGameOne->getEtat()->getId() != $updatedCopyGameOne['etat_id']){
                                    $newEtat = $this->entityManager->getRepository(HmgCopyEtat::class)->findOneBy(['id' => $updatedCopyGameOne['etat_id']]);
                                    if ($newEtat){
                                        $copyGameOne->setEtat($newEtat);
                                    }
                                }
                            } else if ($updatedCopyGameOne['etat_id']){
                                $newEtat = $this->entityManager->getRepository(HmgCopyEtat::class)->findOneBy(['id' => $updatedCopyGameOne['etat_id']]);
                                if ($newEtat){
                                    $copyGameOne->setEtat($newEtat);
                                }
                            }
                            if ($copyGameOne->getFormat()){
                                if ($copyGameOne->getFormat()->getId() != $updatedCopyGameOne['format_id']){
                                    $newFormat = $this->entityManager->getRepository(HmgCopyFormat::class)->findOneBy(['id' => $updatedCopyGameOne['format_id']]);
                                    if ($newFormat){
                                        $copyGameOne->setFormat($newFormat);
                                    }
                                }
                            } else if ($updatedCopyGameOne['format_id']){
                                $newFormat = $this->entityManager->getRepository(HmgCopyFormat::class)->findOneBy(['id' => $updatedCopyGameOne['format_id']]);
                                if ($newFormat){
                                    $copyGameOne->setFormat($newFormat);
                                }
                            }

                            if ($copyGameOne->getRegion()){
                                if ($copyGameOne->getRegion()->getId() != $updatedCopyGameOne['region_id']){
                                    $newRegion = $this->entityManager->getRepository(HmgCopyRegion::class)->findOneBy(['id' => $updatedCopyGameOne['region_id']]);
                                    if ($newRegion){
                                        $copyGameOne->setRegion($newRegion);
                                    }
                                }
                            } else if ($updatedCopyGameOne['region_id']){
                                $newRegion = $this->entityManager->getRepository(HmgCopyRegion::class)->findOneBy(['id' => $updatedCopyGameOne['region_id']]);
                                if ($newRegion){
                                    $copyGameOne->setRegion($newRegion);
                                }
                            }

                            /* GESTION DES LANG*/
                            if (isset($updatedCopyGameOne['hmgLanguages'])){ /* TODO : VERIFI LA CONDITION */

                                $allLang = $updatedCopyGameOne['hmgLanguages'];
                                $langDB = $copyGameOne->getLanguage();

                                /* AJOUTER LES NOUVEAU*/

                                /* ADD */
                                foreach ($allLang as $langOne) {
                                    $found = false;
                                    foreach ($langDB as $langOneDB) {
                                        if ($langOneDB->getId() == $allLang) {
                                            $found = true;
                                            break;
                                        }
                                    }

                                    if (!$found) {
                                        $langToAdd = $this->entityManager->getRepository(HmgCopyLanguage::class)->findOneBy(['id' => $langOne]);
                                        $copyGameOne->addLanguage($langToAdd);
                                    }

                                }


                                /* SUPRESSION */
                                foreach ($langDB as $langOneDB){
                                    $found = false;
                                    foreach ($allLang AS $langOne) {
                                        if ($langOne == $langOneDB->getId()) {
                                            $found = true;
                                            break;
                                        }
                                    }

                                    if (!$found){
                                        $copyGameOne->removeLanguage($langOneDB);
                                    }
                                }

                            }


                            /* GERE LE PURCHASE*/
                            if ($copyGameOne->getPurchase()){
                                if ($copyGameOne->getPurchase()->getId() == $updatedCopyGameOne['purchase']['id']){

                                    $purchase = $copyGameOne->getPurchase();
                                    $newPurchase = $updatedCopyGameOne['purchase'];

                                    if ($purchase->getPrice() != (int)$newPurchase['price'] && $newPurchase['price'] != ""){
                                        $purchase->setPrice((int)$newPurchase['price']);
                                    }
                                    if ($purchase->getContent() != $newPurchase['content'] && $newPurchase['content'] != ""){
                                        $purchase->setContent($newPurchase['content']);
                                    }
                                    if (!empty($newPurchase['day_buy_date'])){
                                        if ($purchase->getDayBuyAt() != (int)$newPurchase['day_buy_date'] && $newPurchase['day_buy_date'] != "") {
                                            $purchase->setDayBuyAt((int)$newPurchase['day_buy_date']);
                                        }
                                    }
                                    if (!empty($newPurchase['month_buy_date'])){
                                        if ($purchase->getMonthBuyAt() != (int)$newPurchase['month_buy_date'] && $newPurchase['month_buy_date'] != "") {
                                            $purchase->setMonthBuyAt((int)$newPurchase['month_buy_date']);
                                        }
                                    }
                                    if (!empty($newPurchase['year_buy_date'])){
                                        if ($purchase->getYearBuyAt() != (int)$newPurchase['year_buy_date'] && $newPurchase['year_buy_date'] != "") {
                                            $purchase->setYearBuyAt((int)$newPurchase['year_buy_date']);
                                        }
                                    }



                                    if ($purchase->getBuyWhere()){
                                        if ($newPurchase['buy_where_id'] == "" || $newPurchase['buy_where_id'] == null || $newPurchase['buy_where_id'] == "null"){
                                            $purchase->setBuyWhere(null);
                                        } else if ($purchase->getBuyWhere()->getId() != $newPurchase['buy_where_id']){
                                            $newBuyWhere = $this->entityManager->getRepository(BuyWhere::class)->findOneBy(['id' => $newPurchase['buy_where_id']]);
                                            if ($newBuyWhere){
                                                $purchase->setBuyWhere($newBuyWhere);
                                            }
                                        }
                                    } else if ($newPurchase['buy_where_id']) {
                                        $newBuyWhere = $this->entityManager->getRepository(BuyWhere::class)->findOneBy(['id' => $newPurchase['buy_where_id']]);
                                        if ($newBuyWhere) {
                                            $purchase->setBuyWhere($newBuyWhere);
                                        }
                                    }

                                    if ($purchase->getDevise()) {
                                        if ($newPurchase['devise_id'] == "" || $newPurchase['devise_id'] == null || $newPurchase['devise_id'] == "null") {
                                            $purchase->setDevise(null);
                                        } else if ($purchase->getDevise()->getId() != $newPurchase['devise_id']) {
                                            $newDevise = $this->entityManager->getRepository(Devise::class)->findOneBy(['id' => $newPurchase['devise_id']]);
                                            if ($newDevise) {
                                                $purchase->setDevise($newDevise);
                                            }
                                        }
                                    } else if ($newPurchase['devise_id']) {
                                        $newDevise = $this->entityManager->getRepository(Devise::class)->findOneBy(['id' => $newPurchase['devise_id']]);
                                        if ($newDevise) {
                                            $purchase->setDevise($newDevise);
                                        }
                                    }

                                    $this->entityManager->persist($purchase);
                                    $this->entityManager->flush();

                                }
                            } else if ($updatedCopyGameOne['purchase']) {

                                /* IL FAUT CREER LE PURCHASE */

                                $purchase = new HmgCopyPurchase();
                                $newPurchase = $updatedCopyGameOne['purchase'];

                                if ($newPurchase['price'] != ""){
                                    $purchase->setPrice((int)$newPurchase['price']);
                                }
                                if ($newPurchase['content'] != ""){
                                    $purchase->setContent($newPurchase['content']);
                                }
                                if ($newPurchase['day_buy_date'] != "" && $newPurchase['day_buy_date'] != null){
                                    $purchase->setDayBuyAt($newPurchase['day_buy_date']);
                                }
                                if ($newPurchase['month_buy_date'] != "" && $newPurchase['month_buy_date'] != null){
                                    $purchase->setMonthBuyAt($newPurchase['month_buy_date']);
                                }
                                if ($newPurchase['year_buy_date'] != "" && $newPurchase['year_buy_date'] != null){
                                    $purchase->setYearBuyAt($newPurchase['year_buy_date']);
                                }
                                $newBuyWhere = $this->entityManager->getRepository(BuyWhere::class)->findOneBy(['id' => $newPurchase['buy_where_id']]);
                                if ($newBuyWhere){
                                    $purchase->setBuyWhere($newBuyWhere);
                                }
                                $newDevise = $this->entityManager->getRepository(Devise::class)->findOneBy(['id' => $newPurchase['devise_id']]);
                                if ($newDevise){
                                    $purchase->setDevise($newDevise);
                                }

                                $this->entityManager->persist($purchase);
                                $this->entityManager->flush();

                                $copyGameOne->setPurchase($purchase);

                            }

                            $this->entityManager->persist($copyGameOne);
                            $this->entityManager->flush();


                            $finalCopyGame[] = $copyGameOne;

                            $found = true;
                            break;
                        }

                    }
                    if (!$found) {
                        $tempAddCopy[] = $updatedCopyGameOne;
                    }
                }

                /* AJOUTER LES NOUVELLE COPY*/
                foreach ($tempAddCopy as $addCopy) {


                    $NEWcopyGame = new HmgCopy();

                    $NEWcopyGame->setHistoryMyGame($historyMyGame);

                    if ($addCopy['edition'] != ""){
                        $NEWcopyGame->setEdition($addCopy['edition']);
                    }
                    if ($addCopy['barcode'] != ""){
                        $NEWcopyGame->setBarcode($addCopy['barcode']);
                    }
                    if ($addCopy['content'] != ""){
                        $NEWcopyGame->setContent($addCopy['content']);
                    }

                    $newEtat = $this->entityManager->getRepository(HmgCopyEtat::class)->findOneBy(['id' => $addCopy['etat_id']]);
                    if ($newEtat){
                        $NEWcopyGame->setEtat($newEtat);
                    }
                    $newFormat = $this->entityManager->getRepository(HmgCopyFormat::class)->findOneBy(['id' => $addCopy['format_id']]);
                    if ($newFormat){
                        $NEWcopyGame->setFormat($newFormat);
                    }
                    $newRegion = $this->entityManager->getRepository(HmgCopyRegion::class)->findOneBy(['id' => $addCopy['region_id']]);
                    if ($newRegion){
                        $NEWcopyGame->setRegion($newRegion);
                    }

                    if (isset($addCopy['hmgLanguages'])) { /* TODO : VERIFI LA CONDITION */

                        $allLang = $addCopy['hmgLanguages'];

                        /* ADD */
                        foreach ($allLang as $langOne) {
                            $langToAdd = $this->entityManager->getRepository(HmgCopyLanguage::class)->findOneBy(['id' => $langOne]);
                            $NEWcopyGame->addLanguage($langToAdd);
                        }
                    }

                    /* GEREZ LE PURCHASE */
                    if ($addCopy['purchase']) {

                        /* IL FAUT CREER LE PURCHASE */

                        $purchase = new HmgCopyPurchase();
                        $newPurchase = $addCopy['purchase'];

                        if ($newPurchase['price'] != ""){
                            $purchase->setPrice((int)$newPurchase['price']);
                        }
                        if ($newPurchase['content'] != ""){
                            $purchase->setContent($newPurchase['content']);
                        }
                        if ($newPurchase['day_buy_date'] != "" && $newPurchase['day_buy_date'] != null){
                            $purchase->setDayBuyAt($newPurchase['day_buy_date']);
                        }
                        if ($newPurchase['month_buy_date'] != "" && $newPurchase['month_buy_date'] != null){
                            $purchase->setMonthBuyAt($newPurchase['month_buy_date']);
                        }
                        if ($newPurchase['year_buy_date'] != "" && $newPurchase['year_buy_date'] != null){
                            $purchase->setYearBuyAt($newPurchase['year_buy_date']);
                        }
                        $newBuyWhere = $this->entityManager->getRepository(BuyWhere::class)->findOneBy(['id' => $newPurchase['buy_where_id']]);
                        if ($newBuyWhere){
                            $purchase->setBuyWhere($newBuyWhere);
                        }
                        $newDevise = $this->entityManager->getRepository(Devise::class)->findOneBy(['id' => $newPurchase['devise_id']]);
                        if ($newDevise){
                            $purchase->setDevise($newDevise);
                        }

                        $this->entityManager->persist($purchase);
                        $this->entityManager->flush();

                        $NEWcopyGame->setPurchase($purchase);

                    }


                    $this->entityManager->persist($NEWcopyGame);
                    $this->entityManager->flush();


                    $finalCopyGame[] = $NEWcopyGame;


                }

                if ($copyGameAll){

                    /* VIDER CEUX QUI NON PAS ETE RENVOYER */
                    foreach ($finalCopyGame as $oneFinalCopy) {

                        foreach ($copyGameAll as $oneOldCopy) {

                            if (!in_array($oneOldCopy->getId(), array_map(function ($copy) {
                                return $copy->getId();
                            }, $finalCopyGame))) {
                                $this->entityManager->remove($oneOldCopy);
                            }

                        }
                        $this->entityManager->flush();
                    }

                }

                if (count($finalCopyGame) == 0){

                    foreach ($copyGameAll as $oneOldCopy) {
                        $this->entityManager->remove($oneOldCopy);
                        $this->entityManager->flush();
                    }

                }


                /* METTRE A JOUR LA NOTE */
                $rate = $this->entityManager->getRepository(UserRate::class)->findOneBy(['game' => $historyMyGame->getGame(), 'user' => $user]);
                if ($rate){
                    $newNote = $data['rate']['rating'];

                    if ($rate->getRating() != $newNote){
                        if (filter_var($newNote, FILTER_VALIDATE_INT) !== false && $newNote >= 0 && $newNote <= 20) {

                            $rate->setRating($newNote);

                        }
                    }

                    if (!empty($data['rate']['content'])){
                        if ($rate->getContent() != $data['rate']['content']){
                            $rate->setContent($data['rate']['content']);
                        }
                    } else {
                        $rate->setContent(null);
                    }

                    $this->entityManager->persist($rate);
                    $this->entityManager->flush();
                } else if (!empty($data['rate']['rating'])){

                    $newNote = $data['rate']['rating'];
                    if (filter_var($newNote, FILTER_VALIDATE_INT) !== false && $newNote >= 0 && $newNote <= 20) {

                        $newRate = new UserRate();
                        $newRate->setRating($newNote);
                        if (!empty($data['rate']['content'])){
                            $newRate->setContent($data['rate']['content']);
                        }
                        $newRate->setGame($historyMyGame->getGame());
                        $newRate->setUser($user);
                        $newRate->setCreatedAt(New \DateTimeImmutable());
                        $newRate->setIp("10.10.10.10");


                        $this->entityManager->persist($newRate);
                        $this->entityManager->flush();
                    }


                }

            } else {
                return $this->json(['message' => 'no permission']);
            }


            /* SPEEDRUN */
            if ($data['speedrun'] || $data['speedrun'] == []){
                $allSpeedrunUpload = $data['speedrun'];

                $onlyCreate = false;
                $speedRunDB = $this->entityManager->getRepository(HmgSpeedrun::class)->findBy(['MyGame' => $historyMyGame]);
                if (!$speedRunDB){
                    $onlyCreate = true;
                }

                foreach ($allSpeedrunUpload as $oneSpeedrunUpload) {

                    $isDB = false;
                    if (!$onlyCreate){
                        foreach ($speedRunDB as $oneSpeedrunDB) {
                            if(isset($oneSpeedrunUpload['id'])){
                                if ($oneSpeedrunDB->getId() == $oneSpeedrunUpload['id']){
                                    $isDB = true;
                                }
                            }
                        }
                    }


                    if ($isDB){
                        /* UPDATE  */
                        $newSpeedrun = $this->entityManager->getRepository(HmgSpeedrun::class)->findOneBy(['id' => $oneSpeedrunUpload['id']]);


                        if (isset($oneSpeedrunUpload['chrono'])) {
                            if ($oneSpeedrunUpload['chrono']) {
                                if ($newSpeedrun->getChrono() != $oneSpeedrunUpload['chrono'] && $oneSpeedrunUpload['chrono'] != "") {
                                    $newSpeedrun->setChrono($oneSpeedrunUpload['chrono']);
                                }
                            }
                        } else {
                          $newSpeedrun->setChrono(null);
                        }

                        if (isset($oneSpeedrunUpload['category'])) {
                            if ($oneSpeedrunUpload['category']) {
                                if ($newSpeedrun->getCategory() != $oneSpeedrunUpload['category'] && $oneSpeedrunUpload['category'] != "") {
                                    $newSpeedrun->setCategory($oneSpeedrunUpload['category']);
                                }
                            }
                        } else {
                            $newSpeedrun->setCategory(null);
                        }
                        if (isset($oneSpeedrunUpload['link'])) {
                            if ($newSpeedrun->getLink() != $oneSpeedrunUpload['link'] && $oneSpeedrunUpload['link'] != "") {
                                $newSpeedrun->setLink($oneSpeedrunUpload['link']);
                            }
                        } else {
                            $newSpeedrun->setLink(null);
                        }


                    } else if (!empty($oneSpeedrunUpload['chrono']) && !empty($oneSpeedrunUpload['category'])) {
                        /* CREER */
                        $newSpeedrun = new HmgSpeedrun();
                        $newSpeedrun->setMyGame($historyMyGame);

                        if (isset($oneSpeedrunUpload['chrono'])) {
                            if ($oneSpeedrunUpload['chrono'] != "") {
                                $newSpeedrun->setChrono($oneSpeedrunUpload['chrono']);
                            }
                        } else {
                            $newSpeedrun->setChrono(null);
                        }

                        if (isset($oneSpeedrunUpload['category'])) {
                            if ($oneSpeedrunUpload['category'] != "") {
                                $newSpeedrun->setCategory($oneSpeedrunUpload['category']);
                            }
                        } else {
                            $newSpeedrun->setCategory(null);
                        }


                        if (isset($oneSpeedrunUpload['link'])) {
                            if ($oneSpeedrunUpload['link'] != "") {
                                $newSpeedrun->setLink($oneSpeedrunUpload['link']);
                            }
                        } else {
                            $newSpeedrun->setLink(null);
                        }

                    }

                    $this->entityManager->persist($newSpeedrun);
                    $this->entityManager->flush();

                }

                /* VERIFIER CEUX QUI ON ETE SUPPRIMER*/
                foreach ($speedRunDB as $oneSpeedrunDB) {
                    $foundInUpload = false;
                    foreach ($allSpeedrunUpload as $oneSpeedrunUpload) {
                        if(isset($oneSpeedrunUpload['id'])) {
                            if ($oneSpeedrunDB->getId() == $oneSpeedrunUpload['id']) {
                                $foundInUpload = true;
                                break;
                            }
                        }
                    }
                    if (!$foundInUpload) {
                        $this->entityManager->remove($oneSpeedrunDB);
                        $this->entityManager->flush();
                    }
                }
                
            }





            $speedRun = $this->entityManager->getRepository(HmgSpeedrun::class)->findBy(['MyGame' => $historyMyGame]);
            $screenshot = $this->entityManager->getRepository(HmgScreenshot::class)->findBy(['MyGame' => $historyMyGame]);

            /* FORMER LE RETOUR*/
            $message = [
                'message' => "updated game",
                'result' => [
                    "id" => $historyMyGame->getId(),
                    "myGame" => $historyMyGame,
                    "copyGame" => $finalCopyGame ?? [],
                    "speedrun" => $speedRun ?? [],
                    "screenshot" => $screenshot ?? [],
                    "rate" => $rate ?? $newRate ?? null,
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
