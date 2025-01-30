<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentReply;
use App\Entity\HistoryMyGame;
use App\Entity\HistoryMyPlateform;
use App\Entity\PostActu;
use App\Entity\User;
use App\Entity\Warn;
use App\Entity\WarnType;
use App\Repository\WarnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WarnController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WarnRepository $warnRepository
    ) {}


    #[Route('/addwarn', name: 'add_warn', methods: ['POST'])]
    public function addWarn(Request $request): JsonResponse
    {
            
        $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        if (!isset($data['warn_type_id'])){
            return $this->json(['message' => 'undefine of field']);
        }

        if (isset($data['profil_id']) || isset($data['actu_id']) || isset($data['comment_id']) || isset($data['comment_reply_id']) || isset($data['hmg_id']) || isset($data['hmp_id'])) {

            if( $this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['profil_id']]) ||
            $this->entityManager->getRepository(PostActu::class)->findOneBy(['id' => $data['actu_id']]) || 
            $this->entityManager->getRepository(Comment::class)->findOneBy(['id' => $data['comment_id']]) ||
            $this->entityManager->getRepository(CommentReply::class)->findOneBy(['id' => $data['comment_reply_id']]) || 
            $this->entityManager->getRepository(HistoryMyGame::class)->findOneBy(['id' => $data['hmg_id']]) || 
            $this->entityManager->getRepository(HistoryMyPlateform::class)->findOneBy(['id' => $data['hmp_id']])
            ) {
                
                $warnType = $this->entityManager->getRepository(WarnType::class)->findOneBy(['id' => $data['warn_type_id']]);
                
                if(!$warnType) {
                    return $this->json(['message' => 'warnType not found']);
                }

                $ip = $request->getClientIp();
                if (!isset($ip)) {
                    $newIp = "0.0.0.0";
                } else {
                    $newIp = $ip;
                }
                
                $authorizationHeader = $request->headers->get('Authorization');
                
                $user = null;
                if (strpos($authorizationHeader, 'Bearer ') === 0) {
                    $token = substr($authorizationHeader, 7);
                    if(!$token){
                        return $this->json(['message' => 'token is failed']);
                    }
                    $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
                    
                    if (!$user) {
                        return $this->json(['message' => 'token is failed']);
                    }
                }
                
                $profil = null;
                $actu = null;
                $comment = null;
                $comment_reply = null;
                $hmg = null;
                $hmp = null;
                
                if ($this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['profil_id']])) {

                    $profil = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['profil_id']]);
                    $isSpam = $this->algoNoSpam($profil, 'profil', $newIp, $user);

                } elseif ($this->entityManager->getRepository(PostActu::class)->findOneBy(['id' => $data['actu_id']])) {

                    $actu = $this->entityManager->getRepository(PostActu::class)->findOneBy(['id' => $data['actu_id']]);
                    $isSpam = $this->algoNoSpam($actu, 'actu', $newIp, $user);

                } elseif ($this->entityManager->getRepository(Comment::class)->findOneBy(['id' => $data['comment_id']])) {
                    
                    $comment = $this->entityManager->getRepository(Comment::class)->findOneBy(['id' => $data['comment_id']]);
                    $isSpam = $this->algoNoSpam($comment, 'comment', $newIp, $user);

                } elseif ($this->entityManager->getRepository(CommentReply::class)->findOneBy(['id' => $data['comment_reply_id']])) {

                    $comment_reply = $this->entityManager->getRepository(CommentReply::class)->findOneBy(['id' => $data['comment_reply_id']]);
                    $isSpam = $this->algoNoSpam($comment_reply, 'comment_reply', $newIp, $user);
                
                } elseif ($this->entityManager->getRepository(HistoryMyGame::class)->findOneBy(['id' => $data['hmg_id']])) {
                    
                    $hmg = $this->entityManager->getRepository(HistoryMyGame::class)->findOneBy(['id' => $data['hmg_id']]);
                    $isSpam = $this->algoNoSpam($hmg, 'hmg', $newIp, $user);

                } elseif ($this->entityManager->getRepository(HistoryMyPlateform::class)->findOneBy(['id' => $data['hmp_id']])) {
                    
                    $hmp = $this->entityManager->getRepository(HistoryMyPlateform::class)->findOneBy(['id' => $data['hmp_id']]);
                    $isSpam = $this->algoNoSpam($hmp, 'hmp', $newIp, $user);

                }

                if ($isSpam === false) {

                    $warn = new Warn();
                    $warn->setWarnType($warnType);
                    $warn->setUser($user);
                    $warn->setProfil($profil);
                    $warn->setActu($actu);
                    $warn->setComment($comment);
                    $warn->setCommentReply($comment_reply);
                    $warn->setHmg($hmg);
                    $warn->setHmp($hmp);
                    $warn->setWarnAt(new \DateTimeImmutable());
                    $warn->setIp($newIp);
                    $warn->setContent($data['content']);
                    $warn->setIsManage(false);
    
                    $this->entityManager->persist($warn);
                    $this->entityManager->flush();
    
                    return $this->json(['message' => 'good', 'result' => $warn], 200, [], ['groups' => 'warn:read']);

                } else {
                    return $this->json(['message' => 'spam']); 
                }
            }

            return $this->json(['messsage' => 'warn not found']);

        }

        return $this->json(['message' => 'undefine of field']);


    }

    #[Route('/warns', name: 'all_warn', methods:['GET'])]
    public function getAllWarn(Request $request): JsonResponse {

        $authorizationHeader = $request->headers->get('Authorization');

        //On vérifie que le token n'est pas vide
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            
            //on verifie que le user existe
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            //on vérifie que le user a bien le role Administrateur
            if (!in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_MODO', $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            //Une fois qu'on sait que c'est bien l'administrateur ou un modérateur on récupère tous les warns
            $warnAll = $this->warnRepository->findBy(['is_manage' => false], ['warnAt' => 'ASC']);

            return $this->json(['message' => 'good', 'result' => $warnAll], 200, [], ['groups' => 'warn:read']);
           
        }

        return $this->json(['message' => 'Token invalide']);

    }

    #[Route('/deletewarn/{id}', name: 'delete_warn', methods:['DELETE'])]
    public function deleteWarn(Request $request, int $id): JsonResponse
    {
        //On verifie qu'on récupère un id
        if (!$id) {
            return $this->json(['message' => 'Id is required']);
        }

        //On verifie que l'id transmit correspond a un Warn
        $myWarn = $this->entityManager->getRepository(Warn::class)->findOneBy(['id' => $id]);
        if (!$myWarn) {
            return $this->json(['message' => 'Warn not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        //On vérifie que le token n'est pas vide
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            
            //on verifie que le user existe
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            //on vérifie que le user a bien le role Administrateur
            if (!in_array('ROLE_ADMIN', $user->getRoles())){
                return $this->json(['message' => 'no permission']);
            }

            //Une fois qu'on sait que c'est bien l'administrateur on supprime le warn
            $this->entityManager->remove($myWarn);
            $this->entityManager->flush();

            return $this->json(['message' => 'delete success']);
        }

        return $this->json(['message' => 'Invalid Token']);
    }

    #[Route('/updatewarn', name: 'update_warn', methods:['PUT'])]
    public function updateWarn(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        // On vérifie si le JSON n'a pas de soucis
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        // On verifie que l'id transmit correspond a un Warn
        $warn = $this->entityManager->getRepository(Warn::class)->findOneBy(['id' => $data['id']]);
        if(!$warn){
            return $this->json(['message' => 'Warn not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        //On vérifie que le token n'est pas vide
        if (strpos($authorizationHeader, 'Bearer ') === 0){
            $token = substr($authorizationHeader, 7);

            //On vérifie que l'utilisateur correspond au token
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'Token is failed']);
            }

            //on vérifie que le user a bien le role Modérateur
            if (!in_array('ROLE_MODO', $user->getRoles())){
                return $this->json(['message' => 'no permission']);
            }

            //on modifie la variable is_manage à true
            $warn->setIsManage(true);
    
            $this->entityManager->persist($warn);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $warn], 200, [], ['groups' => 'warn:read']);

        }

        return $this->json(['message' => 'Invalid Token']);
    }






    function algoNoSpam($object, $type, $ip, $user = null)
    {

        if ($type !== 'profil' && $type !== 'actu' && $type !== 'comment' && $type !== 'comment_reply' && $type !== 'hmg' && $type !== 'hmp') {
            return true; /* LOCK - aucun warn peut etre verifier*/
        }

        /*
         *
         * GEREZ AVEC L'IP
         *
         * */
        $ipIsLock = true;
        if ($ip === "0.0.0.0") {
            return true; /* LOCK - ne pas comtabilisé si on n'a pas pu recupere l'ip */
        } else {

            $latestWarnByIp = $this->warnRepository->findLatestWarnByIpAndType($ip, $object, $type);

            if ($latestWarnByIp !== null) {
                if ($latestWarnByIp && $latestWarnByIp->getWarnAt() && $latestWarnByIp->getWarnAt() >= (new \DateTimeImmutable())->sub(new \DateInterval('PT12H'))) {
                    $ipIsLock = true; /* LOCK - warn dans les 12 dernier heure */
                } else {
                    $ipIsLock = false; /* notLock - warn dans audela des 12 dernier heure  */
                }
            } else {
                $ipIsLock = false; /* notLock - aucun warn */
            }

        }

        /*
         *
         * GEREZ AVEC L'UTILISATEUR
         *
         * */
        $userIsLock = true;
        if ($user !== null) {

            $latestWarnByUser = $this->warnRepository->findLatestWarnByUserAndType($user, $object, $type);

            if ($latestWarnByUser !== null) {
                if ($latestWarnByUser && $latestWarnByUser->getWarnAt() && $latestWarnByUser->getWarnAt() >= (new \DateTimeImmutable())->sub(new \DateInterval('PT12H'))) {
                    $userIsLock = true; /* LOCK - warn dans les 12 dernier heure */
                } else {
                    $userIsLock = false; /* notLock - warn dans audela des 12 dernier heure  */
                }
            } else {
                $userIsLock = false; /* notLock - aucun warn */
            }

        }


        /*
         *
         * VERIFICATION
         *
         * */

//        var_dump($userIsLock);
//        var_dump($ipIsLock);
        if (!$userIsLock && !$ipIsLock) {
            return false;
        }

        return true; /* LOCK FOR DEFAULT */
    }
}
