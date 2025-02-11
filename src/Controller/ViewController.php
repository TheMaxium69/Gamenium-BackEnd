<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\PostActu;
use App\Entity\Provider;
use App\Entity\User;
use App\Entity\View;
use App\Repository\PostActuRepository;
use App\Repository\ViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('view')]
class ViewController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ViewRepository $viewRepository,
        private PostActuRepository $postActuRepository
    ) {}

    #[Route('-actu-add', name: 'app_view_actu_add', methods: ['POST'])]
    public function addActuView(Request $request): Response
    {

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id'])){
            return $this->json(['message' => 'undefine of field']);
        }

        /* SET UNE IP */
        $ip = $request->getClientIp();
        if (!isset($ip)) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $ip;
        }

        $idPostActu = $data['id'];

        /*SI L'ACTU EXISTE*/
        $postActu = $this->entityManager->getRepository(PostActu::class)->findOneBy(['id' => $idPostActu]);
        if (!$postActu) {
            return $this->json(['message' => 'actu is failed']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        }

        $isSpam = $this->algoNoSpam($postActu, 'actu', $newIp, $user);
        if ($isSpam === false) {
            $view = new View();
            $view->setPostActu($postActu);
            if ($user) {
                $view->setWho($user);
            }
            $view->setIp($newIp);
            $view->setViewAt(new \DateTimeImmutable());

            $this->entityManager->persist($view);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $view], 200, [], ['groups' => 'view:read']);
        } else {
            return $this->json(['message' => 'spam']);
        }

    }

    #[Route('-provider-add', name: 'app_view_provider_add', methods: ['POST'])]
    public function addProviderView(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id'])){
            return $this->json(['message' => 'undefine of field']);
        }

        /* SET UNE IP */
        $ip = $request->getClientIp();
        if (!isset($ip)) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $ip;
        }

        $idProvider = $data['id'];

        /*SI LE PROVIDER EXISTE*/
        $provider = $this->entityManager->getRepository(Provider::class)->findOneBy(['id' => $idProvider]);
        if (!$provider){
            return $this->json(['message' => 'provider is failed']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        }

        $isSpam = $this->algoNoSpam($provider, 'provider', $newIp, $user);
        if ($isSpam === false) {
            $view = new View();
            $view->setProvider($provider);
            if ($user) {
                $view->setWho($user);
            }
            $view->setIp($newIp);
            $view->setViewAt(new \DateTimeImmutable());

            $this->entityManager->persist($view);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $view], 200, [], ['groups' => 'view:read']);
        } else {
            return $this->json(['message' => 'spam']);
        }


    }

    #[Route('-game-add', name: 'app_view_game_add', methods: ['POST'])]
    public function addGameView(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id'])){
            return $this->json(['message' => 'undefine of field']);
        }

        /* SET UNE IP */
        $ip = $request->getClientIp();
        if (!isset($ip)) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $ip;
        }

        $idgame = $data['id'];

        /*SI LE JEU EXISTE*/
        $game = $this->entityManager->getRepository(Game::class)->findOneBy(['id' => $idgame]);
        if (!$game){
            return $this->json(['message' => 'game is failed']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        }

        $isSpam = $this->algoNoSpam($game, 'game', $newIp, $user);
        if ($isSpam === false) {

            $view = new View();
            $view->setGame($game);
            if ($user) {
                $view->setWho($user);
            }
            $view->setIp($newIp);
            $view->setViewAt(new \DateTimeImmutable());

            $this->entityManager->persist($view);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $view], 200, [], ['groups' => 'view:read']);
        } else {
            return $this->json(['message' => 'spam']);
        }


    }

    #[Route('-profile-add', name: 'app_view_profile_add', methods: ['POST'])]
    public function addProfileView(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id'])){
            return $this->json(['message' => 'undefine of field']);
        }

        /* SET UNE IP */
        $ip = $request->getClientIp();
        if (!isset($ip)) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $ip;
        }

        $idprofile = $data['id'];

        /*SI L'USER EXISTE*/
        $profile = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $idprofile]);
        if (!$profile){
            return $this->json(['message' => 'profile is failed']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        }

        $isSpam = $this->algoNoSpam($profile, 'profile', $newIp, $user);

        if ($isSpam === false) {
            $view = new View();
            $view->setProfile($profile);
            if ($user) {
                $view->setWho($user);
            }
            $view->setIp($newIp);
            $view->setViewAt(new \DateTimeImmutable());

            $this->entityManager->persist($view);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $view], 200, [], ['groups' => 'view:read']);
        } else {
            return $this->json(['message' => 'spam']);
        }


    }

    #[Route('-actu-show/{idPostActu}', name: 'app_view_actu_show', methods: ['GET'])]
    public function showActuViews(int $idPostActu, Request $request): JsonResponse
    {
        $postActu =$this->postActuRepository->find($idPostActu);

        if(!$postActu) {
            return $this->json(['message' => 'post-actu undefine']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'no permission']);
            }

//            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
//                return $this->json(['message' => 'no permission']);
//            }

            $postActuViews = $this->viewRepository->count(['PostActu' => $postActu]);

            $message = [
                "message" => "good",
                "result" => $postActuViews,
            ];

            return $this->json($message, 200, [], ['groups' => 'view:read']);
        } else {
            return $this->json(['message' => 'no token']);
        }
    }

    #[Route('-provider-show/{idProvider}', name: 'app_view_provider_show', methods: ['GET'])]
    public function showProviderViews(int $idProvider, Request $request): JsonResponse
    {
        $provider =$this->entityManager->getRepository(Provider::class)->find($idProvider);

        if(!$provider) {
            return $this->json(['message' => 'provider undefine']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $provider = $this->viewRepository->count(['Provider' => $provider]);

            $message = [
                "message" => "good",
                "result" => $provider,
            ];

            return $this->json($message, 200, [], ['groups' => 'view:read']);
        } else {
            return $this->json(['message' => 'no token']);
        }
    }

    #[Route('-game-show/{idGame}', name: 'app_view_game_show', methods: ['GET'])]
    public function showGameViews(int $idGame, Request $request): JsonResponse
    {
        $game =$this->entityManager->getRepository(Game::class)->find($idGame);

        if(!$game) {
            return $this->json(['message' => 'game undefine']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
                return $this->json(['message' => 'no have permission']);
            }

            $game = $this->viewRepository->count(['Game' => $game]);

            $message = [
                "message" => "good",
                "result" => $game,
            ];

            return $this->json($message, 200, [], ['groups' => 'view:read']);

        } else {
            return $this->json(['message' => 'no token']);
        }
    }


    function algoNoSpam($object, $type, $ip, $user = null)
    {

        if ($type !== 'actu' && $type !== 'provider' && $type !== 'game' && $type !== 'profile') {
            return true; /* LOCK - aucun vue peut etre verifier*/
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

            $latestViewByIp = $this->viewRepository->findLatestViewByIpAndType($ip, $object, $type);

            if ($latestViewByIp !== null) {
                if ($latestViewByIp && $latestViewByIp->getViewAt() && $latestViewByIp->getViewAt() >= (new \DateTimeImmutable())->sub(new \DateInterval('PT12H'))) {
                    $ipIsLock = true; /* LOCK - vue dans les 12 dernier heure */
                } else {
                    $ipIsLock = false; /* notLock - vue dans audela des 12 dernier heure  */
                }
            } else {
                $ipIsLock = false; /* notLock - aucune vue */
            }

        }

        /*
         *
         * GEREZ AVEC L'UTILISATEUR
         *
         * */
        $userIsLock = true;
        if ($user !== null) {

            $latestViewByUser = $this->viewRepository->findLatestViewByUserAndType($user, $object, $type);

            if ($latestViewByUser !== null) {
                if ($latestViewByUser && $latestViewByUser->getViewAt() && $latestViewByUser->getViewAt() >= (new \DateTimeImmutable())->sub(new \DateInterval('PT12H'))) {
                    $userIsLock = true; /* LOCK - vue dans les 12 dernier heure */
                } else {
                    $userIsLock = false; /* notLock - vue dans audela des 12 dernier heure  */
                }
            } else {
                $userIsLock = false; /* notLock - aucune vue */
            }

        } else {
            $userIsLock = false;
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
