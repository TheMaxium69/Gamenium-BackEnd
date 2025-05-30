<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProviderController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProviderRepository $providerRepository
        ) {}

    #[Route('/providerall', name: 'provider_all', methods:'GET')]
    public function getProviderAll(): JsonResponse
    {
        $providers = $this->providerRepository->findAll();

        if(!$providers){
            return $this->json(['message' => 'Provider not found']);
        } else {
            $message = [
                'message' => "good",
                'result' => $providers
            ];

            return $this->json($message , 200 , [], ['groups' => 'provider:read']);
        }


    }

    #[Route('/provider/{id}', name: 'provider_by_id', methods:'GET')]
    public function getProviderById(int $id): JsonResponse
    {
        $provider = $this->providerRepository->find($id);

        if(!$provider){
            return $this->json(['message' => 'Provider not found']);
        } else {
            $message = [
                'message' => "good",
                'result' => $provider
            ];

            return $this->json($message  , 200 , [], ['groups' => 'provider:read']);
        }

    }

//    #[Route('/provider', name:'provider_create', methods:'POST')]
//    public function createProvider(Request $request): JsonResponse
//    {
//        $data = json_decode($request->getContent(), true);
//
//        $provider = new Provider();
//        $provider->setTagName($data['name']);
//        $provider->setDisplayName($data['displayName']);
//        $provider->setCountry($data['country']);
//        $provider->setCreatedAt(new \DateTimeImmutable());
//        $provider->setJoindeAt(new \DateTimeImmutable());
//        $provider->setPicture($data['picture']);
//        $provider->setParentCompany($data['parentCompany']);
//        $provider->setContent($data['content']);
//        $provider->setBanner($data['banner']);
//
//        $this->entityManager->persist($provider);
//        $this->entityManager->flush();
//
//        return $this->json(['message' => 'Provider created successfully'], Response::HTTP_CREATED);
//
//    }
//
//    #[Route('/provider/{id}', name:'provider_delete', methods:'DELETE')]
//    public function deleteProvider(int $id): JsonResponse
//    {
//        $provider = $this->providerRepository->find($id);
//
//        if (!$provider) {
//            return $this->json(['message' => 'Badge not found'], Response::HTTP_NOT_FOUND);
//        }
//
//        $this->entityManager->remove($provider);
//        $this->entityManager->flush();
//
//        return $this->json(['message' => 'Provide deleted successfully']);
//    }

    #[Route('/providers/search', name: 'providers', methods: ['POST'])]
    public function searchUsers(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $searchValue = $data['searchValue'] ?? '';
        $limit = $data['limit'];

        $results = $this->providerRepository->searchProviderByName($searchValue, $limit);
    
        return $this->json($results, 200, [], ['groups' => 'provider:read','picture:read']);
    }
}

