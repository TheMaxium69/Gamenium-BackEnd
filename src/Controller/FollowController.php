<?php

namespace App\Controller;

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

    #[Route ('follow/provider/{id}', name: 'get_follow_count_by_provider', methods: ['GET'])]
    public function getFollowCountByProvider(int $id): JsonResponse
    {
        $followCount = $this->followRepository->count(['provider' => $id]);

        $followProvider = $this->followRepository->findBy(['provider' => $id]);

        $response = [
            'message' => 'good',
            'total' => $followCount,
            'result' => array_map(fn($follow) => ['id' => $follow->getProvider()->getId()], $followProvider)
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

#[Route('/provider/{providerId}/follow', name: 'provider_follow' , methods: ['GET'])]
public function providerLikes(int $providerId): JsonResponse
{
    $provider = $this->providerRepository->find($providerId);

    if (!$provider) {
        throw $this->createNotFoundException('Provider non trouvé');
    }

    $follows = $this->followRepository->findBy(['provider' => $provider]);

    $totalLikes = 0;
    foreach ($follows as $follow) {
        if ($follow->getProvider()) {
            $totalLikes++;
        }
    }
    return $this->json('Nombre total de follow pour le provider ' . $providerId . ' : ' . $totalLikes);
}


    #[Route('/follow', name: 'add_follow', methods: ['POST'])]
    public function addFollow(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['provider_id'], $data['user_id'])) {
            return new JsonResponse(['error' => 'Les données fournies sont incomplètes'], Response::HTTP_BAD_REQUEST);
        }

        $follow = new Follow();
        $follow->setProvider($this->providerRepository->find($data['provider_id']));
        $follow->setUser($this->userRepository->find($data['user_id']));
        $follow->setIp($data['ip']);
        $follow->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($follow);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Le suivi a été ajouté avec succès'], Response::HTTP_CREATED);
    }
}