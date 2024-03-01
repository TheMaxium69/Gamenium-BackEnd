<?php

namespace App\Controller;

use App\Entity\SocialNetwork;
use App\Repository\SocialNetworkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SocialNetworkController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SocialNetworkRepository $socialNetworkRepository
    ) {}

    #[Route('/socialnetworks', name: 'get_all_socialnetworks', methods: ['GET'])]
    public function getAllSocialNetworks(): JsonResponse
    {
        $socialNetworks = $this->socialNetworkRepository->findAll();

        return $this->json($socialNetworks , 200 , [], ['groups' => 'socialnetwork:read']);
    }

    #[Route('/socialnetwork/{id}', name: 'get_socialnetwork_by_id', methods: ['GET'])]
    public function getSocialNetworkById(int $id): JsonResponse
    {
        $socialNetwork = $this->socialNetworkRepository->find($id);

        if (!$socialNetwork) {
            return $this->json(['message' => 'Social Network not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($socialNetwork);
    }

    #[Route('/socialnetwork', name: 'create_socialnetwork', methods: ['POST'])]
    public function createSocialNetwork(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $socialNetwork = new SocialNetwork();
        $socialNetwork->setName($data['name']);
        $socialNetwork->setUrlApi($data['url_api']);

        $this->entityManager->persist($socialNetwork);
        $this->entityManager->flush();

        return $this->json(['message' => 'Social Network created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/socialnetwork/{id}', name: 'delete_socialnetwork', methods: ['DELETE'])]
    public function deleteSocialNetwork(int $id): JsonResponse
    {
        $socialNetwork = $this->socialNetworkRepository->find($id);

        if (!$socialNetwork) {
            return $this->json(['message' => 'Social Network not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($socialNetwork);
        $this->entityManager->flush();

        return $this->json(['message' => 'Social Network deleted successfully']);
    }
}
