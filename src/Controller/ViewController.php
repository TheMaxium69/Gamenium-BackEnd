<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\PostActu;
use App\Entity\Provider;
use App\Entity\User;
use App\Entity\View;
use App\Repository\CommentRepository;
use App\Repository\PostActuRepository;
use App\Repository\UserRepository;
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
        private CommentRepository $commentRepository,
        private UserRepository $userRepository,
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
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
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
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
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
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
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
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
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


    }

    #[Route('-actu-show/{}', name: 'app_view_actu_show', methods: ['GET'])]
    public function showActuViews(int $idPostActu): JsonResponse
    {
        $postActu =$this->postActuRepository->find($idPostActu);

        if(!$postActu) {
            return $this->json(['message' => 'post-actu undefine']);
        }

        $postActuViews = $this->viewRepository->findBy(['post' => $postActu]);

        if (empty($postActuViews)) {
            $message = [
                "message" => "good",
                "result" => []
            ];
            return $this->json($message);
        }

        $message = [
            "message" => "good",
            "reslut" => $postActuViews,
        ];

        return $this->json($message, 200, [], ['groups' => 'view:read']);
    }

}
