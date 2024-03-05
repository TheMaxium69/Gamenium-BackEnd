<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FollowRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\UserRepository;

class FollowController extends AbstractController
{
    private FollowRepository $followRepository;

    public function __construct(UserRepository $userRepository, FollowRepository $followRepository)
    {
        $this->userRepository = $userRepository;
        $this->followRepository = $followRepository;
    }

    #[Route('/follow/provider/{id}', name: 'get_follow_count_by_provider', methods: ['GET'])]
    public function getFollowCountByProvider(int $id): JsonResponse
    {

        $users = $this->userRepository->findById($id);
        
        $followCount = $this->followRepository->count(['provider' => $id]);


        $followProvider = $this->followRepository->findBy(['provider' => $id]);

        $response = [
            'message' => 'good',
            'total' => $followCount,
            'result' => array_map(fn($user) => ['id' => $user->getId(), 'token' => $user->getToken()], $users)
        ];

        return $this->json($response);
    }

    #[Route ('follow/user/{id}', name: 'get_follow_count_by_user', methods: ['GET'])]
    public function getFollowCountByUser(int $id): JsonResponse
    {
        $followCount = $this->followRepository->count(['user' => $id]);

        $followUser = $this->followRepository->findBy(['user' => $id]);

        $response = [
            'message' => 'good',
            'total' => $followCount,
            'result' => array_map(fn($follow) => ['id' => $follow->getUser()->getId()], $followUser)
        ];
        return $this->json($response);
}

}