<?php

namespace App\Controller;

use App\Entity\BuyWhere;
use App\Entity\Devise;
use App\Entity\HistoryMyPlateform;
use App\Entity\HmgCopyEtat;
use App\Entity\HmgCopyPurchase;
use App\Entity\HmgCopyRegion;
use App\Entity\HmpCopy;
use App\Entity\Plateform;
use App\Entity\User;
use App\Repository\HistoryMyPlateformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryMyPlateformController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HistoryMyPlateformRepository $historyMyPlateformRepository
    ){}

    #[Route('/OneMyPlateformByUserWithPlatform/{id_user}/{id_plateform}', name: 'get_one_hmp_by_user', methods: ['GET'])]
    public function getOneMyHmpByUser(int $id_user, int $id_plateform): Response
    {

        $plateform = $this->entityManager->getRepository(Plateform::class)->find($id_plateform);
        if (!$plateform){
            return $this->json(['message' => 'plateform not found']);
        }

        $user = $this->entityManager->getRepository(User::class)->find($id_user);
        if (!$user){
            return $this->json(['message' => 'user not found']);
        }


        $myPlateform = $this->historyMyPlateformRepository->findOneBy(['user' => $user, 'plateform' => $plateform]);
        if (!$myPlateform){
            return $this->json(['message' => 'hmp not found', 'result2' => $plateform], 200, [], ['groups' => 'historyplateform:read']);
        }

        $copyPlateform = $this->entityManager->getRepository(HmpCopy::class)->findBy(['history_my_plateform' => $myPlateform]);

        $message = [
            'message' => "good",
            'result' => [
                "id" => $myPlateform->getId(),
                "myPlateform" => $myPlateform,
                "copyPlateform" => $copyPlateform
            ]
        ];

        return $this->json($message, 200, [], ['groups' => 'historyplateform:read']);
    }

    #[Route('OneMyPlatform/{id_history_my_platform}', name: 'get_one_hmp_by_id', methods:['GET'])]
    public function getOneMyHmpById(int $id_history_my_platform): Response
    {
        $myPlatform = $this->historyMyPlateformRepository->find($id_history_my_platform);

        if (!$myPlatform){
            return $this->json(['message' => 'hmp not found']);
        }

        $copyPlatform = $this->entityManager->getRepository(HmpCopy::class)->findBy(['history_my_plateform' => $myPlatform]);

        $message = [
            'message' => "good",
            'result' => [
                "id" => $myPlatform->getId(),
                "myPlateform" => $myPlatform,
                "copyPlateform" => $copyPlatform
            ]
        ];

        return $this->json($message, 200, [], ['groups' => 'historyplateform:read']);

    }

    #[Route('/addHmp', name: 'addHmp', methods: ['POST'])]
    public function addHmp(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id_plateform'])){
            return $this->json(['message' => 'undefine of field']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0){
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            $plateform = $this->entityManager->getRepository(Plateform::class)->findOneBy(['id' => $data['id_plateform']]);
            if (!$plateform){
                return $this->json(['message' => 'plateform is failed']);
            }

            /*SI LA PLATEFORME A DEJA ETE AJOUTER*/
            $MyPlateformSelectedToUser = $this->historyMyPlateformRepository->findOneBy(['user' => $user, 'plateform' => $plateform]);
            if ($MyPlateformSelectedToUser){
                return $this->json(['message' => 'has already been added']);
            }

            $historyMyPlateform = new HistoryMyPlateform();
            $historyMyPlateform->setUser($user);
            $historyMyPlateform->setPlateform($plateform);
            $historyMyPlateform->setAddedAt(new \DateTimeImmutable());

            $this->entityManager->persist($historyMyPlateform);

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

            $newCopy = new HmpCopy();
            $newCopy->setHistoryMyPlateform($historyMyPlateform);
            $newCopy->setPurchase($newPurchase);
            
            $this->entityManager->persist($newCopy);

            $this->entityManager->flush();

            $result = [
                "id" => $historyMyPlateform->getId(),
                "myPlateform" => $historyMyPlateform,
                "copyPlateform" => [$newCopy]
            ];

            return $this->json(['message' => 'add plateform is collection', 'result' => $result], 200, [], ['groups' => 'historyplateform:read']);
        }

        return $this->json(['message' => 'no token']);
    }

    #[Route('/deleteHmp/{id}', name: 'deleteHmp', methods: ['DELETE'])]
    public function deleteHmp(Request $request, int $id): JsonResponse
    {
        //On verifie qu'on récupère un id
        if (!$id) {
            return $this->json(['message' => 'id is required']);
        }

        //On verifie que l'id transmit correspond a une Hmp
        $myPlateform = $this->entityManager->getRepository(HistoryMyPlateform::class)->findOneBy(['id' => $id]);
        if (!$myPlateform) {
            return $this->json(['message' => 'Plateforme introuvable']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        //On vérifie que le token n'est pas vide
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            
            //on verifie que l'user existe
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            //On verifie si l'user est bien celui rattaché au hmp
            if ($myPlateform->getUser() != $user){
                return $this->json(['message' => 'no permission']);
            }

            //On recupere l'exemplaire
            $myPlateformCopy = $this->entityManager->getRepository(HmpCopy::class)->findBy(['history_my_plateform' => $myPlateform]);

            //Si il y a des exemplaire on les remove
            if($myPlateformCopy) {
                foreach ($myPlateformCopy as $oneMyPlateformCopy) {
                    $this->entityManager->remove($oneMyPlateformCopy);
                }
            }

            /* VERIFIER QUIL PAS DE WARN ET LE SUPP */

            //Une fois qu'on sait qu'il n'y a plus d'exemplaire on veut supprimer le hmp
            $this->entityManager->remove($myPlateform);
            $this->entityManager->flush();

            return $this->json(['message' => 'delete success']);
        }

        return $this->json(['message' => 'Token manquant']);
    } 

    #[Route('/updateMyPlateform', name: 'updateMyPlateform', methods: ['PUT'])]
    public function updateMyPlateform(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        /*On vérifie si le JSON n'a pas de soucis*/
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {

            return $this->json(['message' => 'Format JSON invalide']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        //On vérifie que le token n'est pas vide
        if (strpos($authorizationHeader, 'Bearer ') === 0){
            $token = substr($authorizationHeader, 7);

            //On vérifie que l'utilisateur correspond au token
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'Token invalide']);
            }

            //On verifie que le Hmp correspond
            $historyMyPlateform = $this->entityManager->getRepository(HistoryMyPlateform::class)->findOneBy(['id' => $data['myPlateform']['id']]);
            if(!$historyMyPlateform){
                return $this->json(['message' => 'plateforme introuvable']);
            }

            //On vérifie que l'utilisateur est le bon
            if($historyMyPlateform->getUser() == $user) {

                $copyPlateformAll = $this->entityManager->getRepository(HmpCopy::class)->findBy(['history_my_plateform' => $historyMyPlateform]);

                $updatedCopyPlateformAll = $data['copyPlateform'];

                $tempAddCopy = [];
                $finalCopyGame = [];

                foreach ($updatedCopyPlateformAll as $updatedCopyPlateformOne) {

                    $found = false;

                    foreach ($copyPlateformAll as $copyPlateformOne) {

                        if ($copyPlateformOne->getId() === $updatedCopyPlateformOne['id']) {
                            /* EDIT SA */

                            if ($copyPlateformOne->getEdition() != $updatedCopyPlateformOne['edition']){
                                $copyPlateformOne->setEdition($updatedCopyPlateformOne['edition']);
                            }
                            if ($copyPlateformOne->getBarcode() != $updatedCopyPlateformOne['barcode']){
                                $copyPlateformOne->setBarcode($updatedCopyPlateformOne['barcode']);
                            }
                            if ($copyPlateformOne->getContent() != $updatedCopyPlateformOne['content']){
                                $copyPlateformOne->setContent($updatedCopyPlateformOne['content']);
                            }
                            if ($copyPlateformOne->isIsBox() != $updatedCopyPlateformOne['isBox']){
                                $copyPlateformOne->setIsBox($updatedCopyPlateformOne['isBox']);
                            }

                            if ($copyPlateformOne->getEtat()){
                                if ($copyPlateformOne->getEtat()->getId() != $updatedCopyPlateformOne['etat_id']){
                                    $newEtat = $this->entityManager->getRepository(HmgCopyEtat::class)->findOneBy(['id' => $updatedCopyPlateformOne['etat_id']]);
                                    if ($newEtat){
                                        $copyPlateformOne->setEtat($newEtat);
                                    }
                                }
                            } else if ($updatedCopyPlateformOne['etat_id']){
                                $newEtat = $this->entityManager->getRepository(HmgCopyEtat::class)->findOneBy(['id' => $updatedCopyPlateformOne['etat_id']]);
                                if ($newEtat){
                                    $copyPlateformOne->setEtat($newEtat);
                                }
                            }

                            if ($copyPlateformOne->getRegion()){
                                if ($copyPlateformOne->getRegion()->getId() != $updatedCopyPlateformOne['region_id']){
                                    $newRegion = $this->entityManager->getRepository(HmgCopyRegion::class)->findOneBy(['id' => $updatedCopyPlateformOne['region_id']]);
                                    if ($newRegion){
                                        $copyPlateformOne->setRegion($newRegion);
                                    }
                                }
                            } else if ($updatedCopyPlateformOne['region_id']){
                                $newRegion = $this->entityManager->getRepository(HmgCopyRegion::class)->findOneBy(['id' => $updatedCopyPlateformOne['region_id']]);
                                if ($newRegion){
                                    $copyPlateformOne->setRegion($newRegion);
                                }
                            }

                            /* GESTION DES LANG*/
                            // if (isset($updatedCopyPlateformOne['hmgLanguages'])){ 

                            //     $allLang = $updatedCopyPlateformOne['hmgLanguages'];
                            //     $langDB = $copyPlateformOne->getLanguage();

                            //     foreach ($allLang as $langOne) {
                            //         $found = false;
                            //         foreach ($langDB as $langOneDB) {
                            //             if ($langOneDB->getId() == $allLang) {
                            //                 $found = true;
                            //                 break;
                            //             }
                            //         }

                            //         if (!$found) {
                            //             $langToAdd = $this->entityManager->getRepository(HmgCopyLanguage::class)->findOneBy(['id' => $langOne]);
                            //             $copyPlateformOne->addLanguage($langToAdd);
                            //         }

                            //     }


                            //     foreach ($langDB as $langOneDB){
                            //         $found = false;
                            //         foreach ($allLang AS $langOne) {
                            //             if ($langOne == $langOneDB->getId()) {
                            //                 $found = true;
                            //                 break;
                            //             }
                            //         }

                            //         if (!$found){
                            //             $copyPlateformOne->removeLanguage($langOneDB);
                            //         }
                            //     }

                            // }


                            /* GERE LE PURCHASE*/
                            if ($copyPlateformOne->getPurchase()){
                                if ($copyPlateformOne->getPurchase()->getId() == $updatedCopyPlateformOne['purchase']['id']){

                                    $purchase = $copyPlateformOne->getPurchase();
                                    $newPurchase = $updatedCopyPlateformOne['purchase'];

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
                            } else if ($updatedCopyPlateformOne['purchase']) {

                                /* IL FAUT CREER LE PURCHASE */

                                $purchase = new HmgCopyPurchase();
                                $newPurchase = $updatedCopyPlateformOne['purchase'];

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

                                $copyPlateformOne->setPurchase($purchase);

                            }

                            $this->entityManager->persist($copyPlateformOne);
                            $this->entityManager->flush();


                            $finalCopyGame[] = $copyPlateformOne;

                            $found = true;
                            break;
                        }

                    }
                    if (!$found) {
                        $tempAddCopy[] = $updatedCopyPlateformOne;
                    }
                }

                /* AJOUTER LES NOUVELLE COPY*/
                foreach ($tempAddCopy as $addCopy) {


                    $NEWcopyPlateform = new HmpCopy();

                    $NEWcopyPlateform->sethistoryMyPlateform($historyMyPlateform);

                    if ($addCopy['edition'] != ""){
                        $NEWcopyPlateform->setEdition($addCopy['edition']);
                    }
                    if ($addCopy['barcode'] != ""){
                        $NEWcopyPlateform->setBarcode($addCopy['barcode']);
                    }
                    if ($addCopy['content'] != ""){
                        $NEWcopyPlateform->setContent($addCopy['content']);
                    }
                    if ($addCopy['isBox'] != ""){
                        $NEWcopyPlateform->setIsBox($addCopy['isBox']);
                    }

                    $newEtat = $this->entityManager->getRepository(HmgCopyEtat::class)->findOneBy(['id' => $addCopy['etat_id']]);
                    if ($newEtat){
                        $NEWcopyPlateform->setEtat($newEtat);
                    }

                    $newRegion = $this->entityManager->getRepository(HmgCopyRegion::class)->findOneBy(['id' => $addCopy['region_id']]);
                    if ($newRegion){
                        $NEWcopyPlateform->setRegion($newRegion);
                    }

                    // if (isset($addCopy['hmgLanguages'])) {

                    //     $allLang = $addCopy['hmgLanguages'];

                        /* ADD */
                    //     foreach ($allLang as $langOne) {
                    //         $langToAdd = $this->entityManager->getRepository(HmgCopyLanguage::class)->findOneBy(['id' => $langOne]);
                    //         $NEWcopyPlateform->addLanguage($langToAdd);
                    //     }
                    // }

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

                        $NEWcopyPlateform->setPurchase($purchase);

                    }


                    $this->entityManager->persist($NEWcopyPlateform);
                    $this->entityManager->flush();


                    $finalCopyGame[] = $NEWcopyPlateform;


                }

                if ($copyPlateformAll){

                    /* VIDER CEUX QUI NON PAS ETE RENVOYER */
                    foreach ($finalCopyGame as $oneFinalCopy) {

                        foreach ($copyPlateformAll as $oneOldCopy) {

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

                    foreach ($copyPlateformAll as $oneOldCopy) {
                        $this->entityManager->remove($oneOldCopy);
                        $this->entityManager->flush();
                    }

                }

            } else {
                return $this->json(['message' => 'no permission']);
            }

            $message = [
                'message'   => 'plateforme modifiée',
                'result'    => [
                    'id' => $historyMyPlateform->getId(),
                    'myPlateform' => $historyMyPlateform,
                    'copyPlateform' => $finalCopyGame
                ]
            ];



            return $this->json($message, 200, [], ['groups' => 'historyplateform:read']);

        }

        return $this->json(['message' => 'Token manquant']);
    }
    
}
