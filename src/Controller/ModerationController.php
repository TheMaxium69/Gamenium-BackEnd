<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentReply;
use App\Entity\Game;
use App\Entity\HistoryMyGame;
use App\Entity\HistoryMyPlateform;
use App\Entity\HmgCopy;
use App\Entity\HmgCopyPurchase;
use App\Entity\HmgScreenshot;
use App\Entity\HmgSpeedrun;
use App\Entity\HmgTags;
use App\Entity\HmpCopy;
use App\Entity\Log;
use App\Entity\Picture;
use App\Entity\Plateform;
use App\Entity\PostActu;
use App\Entity\Provider;
use App\Entity\User;
use App\Entity\UserRate;
use App\Entity\Warn;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('modo')]
class ModerationController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('-comment', name: 'app_moderation_comment', methods:['POST'])]
    public function moderateDeleteComment(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        if (!isset($data['comment_id'])) {
            return $this->json(['message' => 'undefine of field']);
        }

        $comment = $this->entityManager->getRepository(Comment::class)->find(['id' => $data['comment_id']]);

        if(!$comment) {
            return $this->json(['message' => 'comment not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderated = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderated) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderated->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $user = $comment->getUser(); /* Auteur de ce comment */

            // IS_DELETE COMMENTAIRE
            $comment->setIsDeleted(true);
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            /* FOR LOG */
            $newLog = new Log();
            $newLog->setWhy("COMMENT DELETE");
            $newLog->setUser($user);
            $newLog->setModeratedBy($moderated);
            $newLog->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($newLog);
            $this->entityManager->flush();
            /* FOR LOG */

            // GESTION DE TOUTE LES WARN DE CE COMMENT
            $warnCommentAll = $this->entityManager->getRepository(Warn::class)->findBy(['comment' => $comment]);
            foreach ($warnCommentAll as $warnCommentOne) {
                $warnCommentOne->setIsManage(true);
                $warnCommentOne->setModeratedBy($moderated);
                $this->entityManager->persist($warnCommentOne);
            }

            // GESTION DES ENFANTS (COMMENT REPLY)
            $commentReplyAll = $this->entityManager->getRepository(CommentReply::class)->findBy(['comment' => $comment]);
            foreach ($commentReplyAll as $commentReplyOne) {
                $commentReplyOne->setIsDeleted(true);
                $this->entityManager->persist($commentReplyOne);

                $warnCommentReplyAll = $this->entityManager->getRepository(Warn::class)->findBy(['commentReply' => $commentReplyOne]);
                foreach ($warnCommentReplyAll as $warnCommentReplyOne) {
                    $warnCommentReplyOne->setIsManage(true);
                    $warnCommentReplyOne->setModeratedBy($moderated);
                    $this->entityManager->persist($warnCommentReplyOne);
                }
            }
            $this->entityManager->flush();

            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }

    }
    
    #[Route('-comment-reply', name: 'app_moderation_comment-reply', methods:['POST'])]
    public function moderateDeleteCommentReply(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        if (!isset($data['comment_reply_id'])) {
            return $this->json(['message' => 'undefine of field']);
        }

        $commentReply = $this->entityManager->getRepository(CommentReply::class)->find(['id' => $data['comment_reply_id']]);

        if(!$commentReply) {
            return $this->json(['message' => 'Reply not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderated = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderated) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderated->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $user = $commentReply->getUser();
            $commentReply->setIsDeleted(true);
            $this->entityManager->persist($commentReply);
            $this->entityManager->flush();

            /* FOR LOG */
            $newLog = new Log();
            $newLog->setWhy("COMMENT DELETE");
            $newLog->setUser($user);
            $newLog->setModeratedBy($moderated);
            $newLog->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($newLog);
            $this->entityManager->flush();
            /* FOR LOG */

            $warnReplyAll = $this->entityManager->getRepository(Warn::class)->findBy(['commentReply' => $commentReply]);
            foreach ($warnReplyAll as $warnReplyOne) {
                $warnReplyOne->setIsManage(true);
                $warnReplyOne->setModeratedBy($moderated);
                $this->entityManager->persist($warnReplyOne);
            }
            $this->entityManager->flush();


            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }

    }

    #[Route('-actu', name: 'app_moderation_actu', methods:['POST'])]
    public function moderateDeleteActu(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        if (!isset($data['actu_id'])) {
            return $this->json(['message' => 'undefine of field']);
        }

        $actu = $this->entityManager->getRepository(PostActu::class)->find(['id' => $data['actu_id']]);

        if(!$actu) {
            return $this->json(['message' => 'Actu not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderated = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderated) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderated->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $user = $actu->getUser();
            $actu->setIsDeleted(true);
            $this->entityManager->persist($actu);
            $this->entityManager->flush();

            /* FOR LOG */
            $newLog = new Log();
            $newLog->setWhy("ACTU DELETE");
            $newLog->setUser($user);
            $newLog->setModeratedBy($moderated);
            $newLog->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($newLog);
            $this->entityManager->flush();
            /* FOR LOG */
            
            $warnActuAll = $this->entityManager->getRepository(Warn::class)->findBy(['actu' => $actu]);
            foreach ($warnActuAll as $warnActuOne) {
                $warnActuOne->setIsManage(true);
                $warnActuOne->setModeratedBy($moderated);
                $this->entityManager->persist($warnActuOne);
            }
            $this->entityManager->flush();
            
            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }

    }
    
    #[Route('-pp', name: 'app_moderation_pp', methods:['POST'])]
    public function moderateDeletePP(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        if (!isset($data['profil_id'])) {
            return $this->json(['message' => 'undefine of field']);
        }

        $user = $this->entityManager->getRepository(User::class)->find(['id' => $data['profil_id']]);

        if(!$user) {
            return $this->json(['message' => 'user not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderated = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderated) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderated->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $picture = $user->getPp();

            if ($picture) {
                $picture->setIsDeleted(true);
                $this->entityManager->persist($picture);
    
                // SET NULL DE LA PHOTO
                $user->setPp(null);
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }

            /* FOR LOG */
            $newLog = new Log();
            $newLog->setWhy("PP DELETE");
            $newLog->setUser($user);
            $newLog->setModeratedBy($moderated);
            $newLog->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($newLog);
            $this->entityManager->flush();
            /* FOR LOG */
            
            $warnProfilAll = $this->entityManager->getRepository(Warn::class)->findBy(['profil' => $user]);
            foreach ($warnProfilAll as $warnProfilOne) {
                $warnProfilOne->setIsManage(true);
                $warnProfilOne->setModeratedBy($moderated);
                $this->entityManager->persist($warnProfilOne);
            }
            $this->entityManager->flush();

            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }

    }

    #[Route('-hmp', name: 'app_moderation_hmp', methods:['PUT'])]
    public function moderateHmp(Request $request): JsonResponse {

        $data = $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        if (!isset($data['copyPlateform_id'])) {
            return $this->json(['message' => 'undefine of field']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderated = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderated) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderated->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $copyPlatform = $this->entityManager->getRepository(HmpCopy::class)->find(['id' => $data['copyPlateform_id']]);

            if($copyPlatform) {  
                
                $HmpId = $copyPlatform->getHistoryMyPlateform();
                $user = $HmpId->getUser();
    
               
    
                if(isset($data['edition']) && $data['edition']){
                   $copyPlatform->setEdition(null); 
                } else if (isset($data['barcode']) && $data['barcode']){
                        $copyPlatform->setBarcode(null);
                } else if (isset($data['content']) && $data['content']){
                        $copyPlatform->setContent(null);
                } else if(isset($data['buy_where']) && $data['buy_where']){

                    if ($copyPlatform->getPurchase()){
    
                        $buyWhere = $copyPlatform->getPurchase()->getBuyWhere();
                        $copyPlatform->getPurchase()->setBuyWhere(null);
    
                        if ($buyWhere) {
                            // Récupérer toutes les HmgPurchase associées au buyWhere
                            $hmgPurchases = $this->entityManager->getRepository(HmgCopyPurchase::class)->findBy(['buy_where' => $buyWhere]);
    
                            // Mettre à null le champ buyWhere pour toutes les entités HmgPurchase associées
                            foreach ($hmgPurchases as $hmgPurchase) {
                                $hmgPurchase->setBuyWhere(null);
                                $this->entityManager->persist($hmgPurchase);
                            }
    
                            // Supprimer le buyWhere
                            $copyPlatform->getPurchase()->setBuyWhere(null);
                            $this->entityManager->remove($buyWhere);
                        }
    
                    } else {
                        return $this->json(['message' => 'error, no update']);
                    }
    
    
                } else if (isset($data['purchase_content']) && $data['purchase_content']) {

                    if($copyPlatform->getPurchase()){

                        $copyPlatform->getPurchase()->setContent(null);

                    } else {
                        return $this->json(['message' => 'error no update']);
                    } 

                } else {
                    return $this->json(['message' => 'error, no update']);
                }
                
                $this->entityManager->persist($copyPlatform);
                /* FOR LOG */
                $newLog = new Log();
                $newLog->setWhy("HMP EDITED");
                $newLog->setUser($user);
                $newLog->setModeratedBy($moderated);
                $newLog->setCreatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($newLog);
                /* FOR LOG */
        
   
                $this->entityManager->flush();
        
                    return $this->json(['message' => 'good']);
    
                } else {
                    return $this->json(['message' => 'copy of platform not found']);
                }

        } else {
            return $this->json(['message' => 'no token']);
        }
    }

    #[Route('-hmg', name: 'app_moderation_hmg', methods:['PUT'])]
    public function moderateHmg(Request $request): JsonResponse {
        $data = $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderated = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderated) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderated->getRoles())) {
                return $this->json(['message' => 'no have permission']);
            }


            if(isset($data['copyGame_id']) && $data['copyGame_id']){

                $copyGame = $this->entityManager->getRepository(HmgCopy::class)->find(['id' => $data['copyGame_id']]);
                if (!$copyGame){
                    return $this->json(['message' => 'Object not found']);
                }

                $user = $copyGame->getHistoryMyGame()->getUser();

                if($data['edition']){
                    $copyGame->setEdition(null);
                } else if($data['barcode']){
                    $copyGame->setBarcode(null);
                } else if($data['content']){
                    $copyGame->setContent(null);
                } else if($data['buy_where']){

                    if ($copyGame->getPurchase()){

                        $buyWhere = $copyGame->getPurchase()->getBuyWhere();
                        $copyGame->getPurchase()->setBuyWhere(null);

                        if ($buyWhere) {
                            // Récupérer toutes les HmgPurchase associées au buyWhere
                            $hmgPurchases = $this->entityManager->getRepository(HmgCopyPurchase::class)->findBy(['buy_where' => $buyWhere]);

                            // Mettre à null le champ buyWhere pour toutes les entités HmgPurchase associées
                            foreach ($hmgPurchases as $hmgPurchase) {
                                $hmgPurchase->setBuyWhere(null);
                                $this->entityManager->persist($hmgPurchase);
                            }

                            // Supprimer le buyWhere
                            $copyGame->getPurchase()->setBuyWhere(null);
                            $this->entityManager->remove($buyWhere);
                        }

                    } else {
                        return $this->json(['message' => 'error, no update']);
                    }


                } else if($data['purchase_content']){

                    if ($copyGame->getPurchase()){

                        $copyGame->getPurchase()->setContent(null);

                    } else {
                        return $this->json(['message' => 'error, no update']);
                    }

                } else {
                    return $this->json(['message' => 'error, no update']);
                }

                $this->entityManager->persist($copyGame);


            } else if (isset($data['speedrun_id']) &&  $data['speedrun_id']){

                $speedrun = $this->entityManager->getRepository(HmgSpeedrun::class)->find(['id' => $data['speedrun_id']]);
                if (!$speedrun){
                    return $this->json(['message' => 'Object not found']);
                }

                $user = $speedrun->getMyGame()->getUser();

                if($data['category']){
                    $speedrun->setCategory(null);
                } else if($data['chrono']){
                    $speedrun->setChrono(null);
                } else if($data['link']){
                    $speedrun->setLink(null);
                } else {
                    return $this->json(['message' => 'error, no update']);
                }

                $this->entityManager->persist($speedrun);

            } else if (isset($data['rate_id']) && $data['rate_id']){

                $rate = $this->entityManager->getRepository(UserRate::class)->find(['id' => $data['rate_id']]);
                if (!$rate){
                    return $this->json(['message' => 'Object not found']);
                }

                $user = $rate->getUser();

                $rate->setContent(null);

                $this->entityManager->persist($rate);

            } else if(isset($data['tag_id']) && $data['tag_id']){

                $tag = $this->entityManager->getRepository(HmgTags::class)->find(['id' => $data['tag_id']]);
                if (!$tag){
                    return $this->json(['message' => 'Object not found']);
                }

                $user = $tag->getUser();

                $hmgHaveTags = $tag->getHistoryMyGame();

                foreach ($hmgHaveTags as $hmgHaveTag) {
                    $tag->removeHistoryMyGame($hmgHaveTag);
                }

                $this->entityManager->remove($tag);
            } else if(isset($data['screenshot_id']) && $data['screenshot_id']){

                $screenshot = $this->entityManager->getRepository(HmgScreenshot::class)->findOneBy(['id' => $data['screenshot_id']]);
                if(!$screenshot) {
                    return $this->json(['message' => 'Object not found']);
                }

                $user = $screenshot->getMyGame()->getUser();

                $picture = $screenshot->getPicture();
                $picture->setIsDeleted(true);
                $this->entityManager->persist($picture);

                $this->entityManager->remove($screenshot);
                
            }else{
                return $this->json(['message' => 'undefine of field']);
            }

            /* FOR LOG */
            $newLog = new Log();
            $newLog->setWhy("HMP EDITED");
            $newLog->setUser($user);
            $newLog->setModeratedBy($moderated);
            $newLog->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($newLog);
            /* FOR LOG */

            
            $this->entityManager->flush();

            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }
    }

    #[Route('-profil-random', name: 'app_moderation_profil_random', methods:['GET'])]
    public function randomProfil(Request $request): JsonResponse {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderator = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderator) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderator->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $usersRandom = $this->entityManager->getRepository(User::class)->getRandomUser(2);

            $i = 0;
            foreach ($usersRandom as $userRandom) {

                $tempUser = [];

                $picture = $this->entityManager->getRepository(Picture::class)->findOneBy(['id' => $userRandom['pp_id']]);

                $tempUser = [
                    "id" => $userRandom['id'],
                    "username" => $userRandom['username'],
                    "displayname" => $userRandom['displayname'],
                    "displayname_useritium" => $userRandom['displayname_useritium'],
                    "color" => $userRandom['color'],
                    "pp" => $picture,
                    "roles" => json_decode($userRandom['roles']),
                ];


                if ($i == 0){
                    $firstUser = $tempUser;
                } else if ($i == 1){
                    $secondUser = $tempUser;
                }


                $i++;
            }

            return $this->json(['message' => 'good', "result" => $firstUser, "result2"=>$secondUser ], 200, [], ['groups'=> 'picture:read']);

        } else {
            return $this->json(['message' => 'no token']);
        }
    }


    #[Route('-actu-random', name: 'app_moderation_actu_random', methods:['GET'])]
    public function randomActu(Request $request): JsonResponse {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderator = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderator) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderator->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $actuRandom = $this->entityManager->getRepository(PostActu::class)->getRandomActu(2);

            $i = 0;
            foreach ($actuRandom as $actuRandom) {

                $tempActu = [];

                $picture = $this->entityManager->getRepository(Picture::class)->findOneBy(['id' => $actuRandom['picture_id']]);
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $actuRandom['user_id']]);
                $provider = $this->entityManager->getRepository(Provider::class)->findOneBy(['id' => $actuRandom['provider_id']]);

                $tempActu = [
                    "id" => $actuRandom['id'],
                    "title" => $actuRandom['title'],
                    "picture" => $picture,
                    "user" => $user,
                    "Provider" =>$provider,
                    "content" => $actuRandom['content'],
                    "created_at" => $actuRandom['created_at'],
                ];


                if ($i == 0){
                    $first = $tempActu;
                } else if ($i == 1){
                    $second = $tempActu;
                }


                $i++;
            }

            return $this->json(['message' => 'good', "result" => $first, "result2"=>$second ], 200, [], ['groups'=> 'post:read']);

        } else {
            return $this->json(['message' => 'no token']);
        }
    }

    #[Route('-hmg-random', name: 'app_moderation_hmg_random', methods:['GET'])]
    public function randomHmg(Request $request): JsonResponse {
        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderator = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderator) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderator->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $hmgsRandom = $this->entityManager->getRepository(HistoryMyGame::class)->getRandomHmg(2);

            $i = 0;
            foreach ($hmgsRandom as $hmgRandom) {

                $temphmg = [];

                $game = $this->entityManager->getRepository(Game::class)->findOneBy(['id'=>$hmgRandom['game_id']]);
                $userGame = $this->entityManager->getRepository(User::class)->findOneBy(['id'=>$hmgRandom['user_id']]);
                $platform = $this->entityManager->getRepository(Plateform::class)->findOneBy(['id'=>$hmgRandom['plateform_id']]);


                $temphmg = [
                    "id" => $hmgRandom['id'],
                    "myGame" => [
                        "user" => $userGame,
                        "game" => $game,
                        "plateform" => $platform,
                        "id"=> $hmgRandom['id'],
                        "is_pinned"=> $hmgRandom['is_pinned'],
                        "added_at"=> $hmgRandom['added_at'],
                        "difficulty_rating"=> $hmgRandom['difficulty_rating'],
                        "lifetime_rating"=> $hmgRandom['lifetime_rating']
                    ]
                ];

                if ($i == 0){
                    $firstHmg = $temphmg;
                } else if ($i == 1){
                    $secondHmg = $temphmg;
                }


                $i++;
            }

            return $this->json(['message' => 'good', "result" => $firstHmg, "result2"=>$secondHmg], 200, [], ['groups'=> 'historygame:read']);

        } else {
            return $this->json(['message' => 'no token']);
        }
    }   
    #[Route('-hmp-random', name: 'app_moderation_hmp_random', methods:['GET'])]
    public function randomHmp(Request $request): JsonResponse {
        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderator = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderator) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderator->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $hmpsRandom = $this->entityManager->getRepository(HistoryMyPlateform::class)->getRandomHmp(2);

            $i = 0;
            foreach ($hmpsRandom as $hmpRandom) {

                $temphmp = [];

                $user = $this->entityManager->getRepository(User::class)->findOneBy(['id'=>$hmpRandom['user_id']]);
                $platform = $this->entityManager->getRepository(Plateform::class)->findOneBy(['id'=>$hmpRandom['plateform_id']]);

                $temphmp = [
                    "id" => $hmpRandom['id'],
                    "myPlateform" => [
                        "user" => $user,
                        "plateform" => $platform,
                        "added_at"=> $hmpRandom['added_at'],
                    ]
                ];

                if ($i == 0){
                    $firstHmp = $temphmp;
                } else if ($i == 1){
                    $secondHmp = $temphmp;
                }
                $i++;
            }

            return $this->json(['message' => 'good', "result" => $firstHmp, "result2"=>$secondHmp], 200, [], ['groups'=> 'historyplateform:read']);

        } else {
            return $this->json(['message' => 'no token']);
        }
    }   

    #[Route('-comment-random', name: 'app_moderation_comment_random', methods:['GET'])]
    public function randomComment(Request $request): JsonResponse {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /* SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $moderator = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderator) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderator->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $commentsRandom = $this->entityManager->getRepository(Comment::class)->getRandomComment(2);

            $i = 0;
            foreach ($commentsRandom as $commentRandom) {

                $tempComment = [];

                $userComment = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $commentRandom['user_id']]);

                $tempComment = [
                    "id" => $commentRandom['id'],
                    "content" => $commentRandom['content'],
                    "user" => $userComment,
                ];

                if ($i == 0){
                    $firstComment = $tempComment;
                } else if ($i == 1){
                    $secondComment = $tempComment;
                }


                $i++;
            }

            return $this->json(['message' => 'good', "result" => $firstComment, "result2"=>$secondComment ], 200, [], ['groups'=> 'comment:admin']);

        } else {
            return $this->json(['message' => 'no token']);
        }
    }

    #[Route('-comment-reply-random', name: 'app_moderation_comment_reply_random', methods:['GET'])]
    public function randomCommentReply(Request $request): JsonResponse {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /* SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $moderator = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderator) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderator->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $commentsReplyRandom = $this->entityManager->getRepository(CommentReply::class)->getRandomCommentReply(2);

            $i = 0;
            foreach ($commentsReplyRandom as $commentReplyRandom) {

                $tempCommentReply = [];

                $userCommentReply = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $commentReplyRandom['user_id']]);

                $tempCommentReply = [
                    "id" => $commentReplyRandom['id'],
                    "content" => $commentReplyRandom['content'],
                    "user" => $userCommentReply,
                ];

                if ($i == 0){
                    $firstCommentReply = $tempCommentReply;
                } else if ($i == 1){
                    $secondCommentReply = $tempCommentReply;
                }


                $i++;
            }

            return $this->json(['message' => 'good', "result" => $firstCommentReply, "result2"=>$secondCommentReply ], 200, [], ['groups'=> 'commentreply:admin']);

        } else {
            return $this->json(['message' => 'no token']);
        }
    }
}
