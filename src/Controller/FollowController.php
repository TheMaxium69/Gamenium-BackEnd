<?php

namespace App\Controller;

use App\Entity\GameProfile;
use App\Entity\Provider;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FollowRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\UserRepository;
use App\Entity\Follow;
use App\Repository\ProviderRepository;

class FollowController extends AbstractController
{
    private FollowRepository $followRepository;
    private ProviderRepository $providerRepository;
    private UserRepository $userRepository;
    private entityManagerInterface $entityManager;

    public function __construct(FollowRepository $followRepository, ProviderRepository $providerRepository, UserRepository $userRepository, entityManagerInterface $entityManager)
    {
        $this->followRepository = $followRepository;
        $this->providerRepository = $providerRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;

    }

    #[Route ('followByProvider/{id}', name: 'get_follow_by_provider', methods: ['GET'])]
    public function getFollowByProvider(int $id): JsonResponse
    {
        $provider = $this->entityManager->getRepository(Provider::class)->find($id);

        if (!$provider){

            return $this->json(['message' => 'provider not found']);

        } else {

            $followProvider = $this->followRepository->findBy(['provider' => $id]);


            $message = [
                'message' => "good",
                'result' => $followProvider
            ];


            return $this->json($message, 200, [], ['groups' => 'followProvider:read']);
        }

    }

    #[Route ('followByGameProfil/{id}', name: 'get_follow_by_game_profil', methods: ['GET'])]
    public function getFollowByGameProfil(int $id): JsonResponse
    {
        $profilGame = $this->entityManager->getRepository(GameProfile::class)->find($id);

        if (!$profilGame){

            return $this->json(['message' => 'game profil not found']);

        } else {

            $followGameProfil = $this->followRepository->findBy(['game_profil' => $id]);


            $message = [
                'message' => "good",
                'result' => $followGameProfil
            ];


            return $this->json($message, 200, [], ['groups' => 'followPageGame:read']);
        }

    }


    #[Route ('myFollowByUser/{id}', name: 'get_follow_count_by_user', methods: ['GET'])]
    public function getFollowByUser(int $id): JsonResponse
    {

        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user){

            return $this->json(['message' => 'user not found']);

        } else {

            $followUser = $this->followRepository->findBy(['user' => $id]);


            $message = [
                'message' => "good",
                'result' => $followUser
            ];


            return $this->json($message, 200, [], ['groups' => 'follow:read']);
        }
    }


    #[Route('/followProvider', name: 'add_follow_provider', methods: ['POST'])]
    public function addFollowProvider(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id_provider'])){
            return $this->json(['message' => 'undefine of field']);
        }

        /* SET UNE IP */
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
        }

        $idProvider = $data['id_provider'];

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            /*SI L'ACTU EXISTE*/
            $provider = $this->entityManager->getRepository(Provider::class)->findOneBy(['id' => $idProvider]);
            if (!$provider){
                return $this->json(['message' => 'provider is failed']);
            }

            /*SI IL EST DJA FOLLOW*/
            $isFollow = $this->followRepository->findOneBy(['provider' => $provider, 'user'=>$user]);
            if($isFollow){
                return $this->json(['message' => 'user is follow']);
            }

            $follow = new Follow();
            $follow->setProvider($provider);
            $follow->setUser($user);
            $follow->setIp($newIp);
            $follow->setCreatedAt(new \DateTimeImmutable());


            $this->entityManager->persist($follow);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $follow], 200, [], ['groups' => 'followProvider:read']);

        }

        return $this->json(['message' => 'no token']);

    }

    #[Route('/followGameProfil', name: 'add_follow_game_profil', methods: ['POST'])]
    public function addFollowGameProfil(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id_gameprofil'])){
            return $this->json(['message' => 'undefine of field']);
        }

        /* SET UNE IP */
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
        }

        $idGameProfil = $data['id_gameprofil'];

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            /*SI L'ACTU EXISTE*/
            $gameprofil = $this->entityManager->getRepository(GameProfile::class)->findOneBy(['id' => $idGameProfil]);
            if (!$gameprofil){
                return $this->json(['message' => 'GameProfil is failed']);
            }

            /*SI IL EST DJA FOLLOW*/
            $isFollow = $this->followRepository->findOneBy(['game_profil' => $gameprofil, 'user'=>$user]);
            if($isFollow){
                return $this->json(['message' => 'user is follow']);
            }

            $follow = new Follow();
            $follow->setGameProfile($gameprofil);
            $follow->setUser($user);
            $follow->setIp($newIp);
            $follow->setCreatedAt(new \DateTimeImmutable());


            $this->entityManager->persist($follow);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $follow], 200, [], ['groups' => 'followPageGame:read']);

        }

        return $this->json(['message' => 'no token']);

    }
}