<?php

namespace App\Controller;

use App\Entity\PostActu;
use App\Repository\PostActuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostActuController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PostActuRepository $postActuRepository
    ) {}

    #[Route('/postactus', name: 'get_all_postactus', methods: ['GET'])]
    public function getAllPostActus(): JsonResponse
    {
        $postActus = $this->postActuRepository->findAll();

        if(!$postActus){
            return $this->json(['message' => 'Provider not found']);
        } else {
            $message = [
                'message' => "good",
                'result' => $postActus
            ];

            return $this->json($message);
        }

        return $this->json($message , 200 , [], ['groups' => 'post:read']);
    }

    #[Route('/postactu/{id}', name: 'get_postactu_by_id', methods: ['GET'])]
    public function getPostActuById(int $id): JsonResponse
    {
        $postActu = $this->postActuRepository->find($id);

        if(!$postActu){
            return $this->json(['message' => 'Post Actu not found']);
        } else {
            $message = [
                'message' => "good",
                'result' => $postActu
            ];

            return $this->json($message, 200 , [], ['groups' => 'post:read']);
        }
    }

    #[Route('/postactus', name: 'create_postactu', methods: ['POST'])]
    public function createPostActu(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $postActu = new PostActu();
        $postActu->setCreatedAt(new \DateTimeImmutable($data['created_at']));
        $postActu->setContent($data['content']);
        $postActu->setLastEdit($data['last_edit']);
        $postActu->setNbEdit($data['nb_edit']);

        $this->entityManager->persist($postActu);
        $this->entityManager->flush();

        return $this->json(['message' => 'PostActu created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/postactus/{id}', name: 'delete_postactu', methods: ['DELETE'])]
    public function deletePostActu(int $id): JsonResponse
    {
        $postActu = $this->postActuRepository->find($id);

        if (!$postActu) {
            return $this->json(['message' => 'PostActu not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($postActu);
        $this->entityManager->flush();

        return $this->json(['message' => 'PostActu deleted successfully']);
    }
}
