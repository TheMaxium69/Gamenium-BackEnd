<?php

namespace App\Controller;

use App\Entity\MyAccountExterne;
use App\Repository\MyAccountExterneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyAccountExterneController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MyAccountExterneRepository $myAccountExterneRepository
    ) {}

    #[Route('/myaccountexternes', name: 'get_all_myaccountexternes', methods: ['GET'])]
    public function getAllMyAccountExternes(): JsonResponse
    {
        $myAccountExternes = $this->myAccountExterneRepository->findAll();

        return $this->json($myAccountExternes);
    }

    #[Route('/myaccountexterne/{id}', name: 'get_myaccountexterne_by_id', methods: ['GET'])]
    public function getMyAccountExterneById(int $id): JsonResponse
    {
        $myAccountExterne = $this->myAccountExterneRepository->find($id);

        if (!$myAccountExterne) {
            return $this->json(['message' => 'MyAccountExterne not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($myAccountExterne);
    }
//
//    #[Route('/myaccountexterne', name: 'create_myaccountexterne', methods: ['POST'])]
//    public function createMyAccountExterne(Request $request): JsonResponse
//    {
//        $data = json_decode($request->getContent(), true);
//
//        $myAccountExterne = new MyAccountExterne();
//        $myAccountExterne->setIdNetwork($data['id_network']);
//        $myAccountExterne->setIdUser($data['id_user']);
//        $myAccountExterne->setCreatedAt(new \DateTimeImmutable());
//        $myAccountExterne->setApiKey($data['api_key']);
//
//        $this->entityManager->persist($myAccountExterne);
//        $this->entityManager->flush();
//
//        return $this->json(['message' => 'MyAccountExterne created successfully'], Response::HTTP_CREATED);
//    }
//
//    #[Route('/myaccountexterne/{id}', name: 'delete_myaccountexterne', methods: ['DELETE'])]
//    public function deleteMyAccountExterne(int $id): JsonResponse
//    {
//        $myAccountExterne = $this->myAccountExterneRepository->find($id);
//
//        if (!$myAccountExterne) {
//            return $this->json(['message' => 'MyAccountExterne not found'], Response::HTTP_NOT_FOUND);
//        }
//
//        $this->entityManager->remove($myAccountExterne);
//        $this->entityManager->flush();
//
//        return $this->json(['message' => 'MyAccountExterne deleted successfully']);
//    }
}
