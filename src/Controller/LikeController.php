<?php

namespace App\Controller;

use App\Entity\Like;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LikeRepository $likeRepository
    ) {}

    #[Route('/likes', name: 'get_all_likes', methods: ['GET'])]
    public function getAlllikes(): JsonResponse
    {
        $likes = $this->likeRepository->findAll();

        return $this->json($likes);
    }

    #[Route('/like/{id}', name: 'get_like_by_id', methods: ['GET'])]
    public function getlikeById(int $id): JsonResponse
    {
        $like = $this->likeRepository->find($id);

        if (!$like) {
            return $this->json(['message' => 'like not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($like);
    }

    #[Route('/like', name: 'create_like', methods: ['POST'])]
    public function createlike(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $like = new Like();
        $like->setIdPost($data['idPost']);
        $like->setIdComment($data['idComment']);
        $like->setIdUser($data['idUser']);
        $like->setIp($data['ip']);
        $like->setCreatedAt(new \DateTimeImmutable());


        $this->entityManager->persist($like);
        $this->entityManager->flush();

        return $this->json(['message' => 'like created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/like/{id}', name: 'delete_like', methods: ['DELETE'])]
    public function deletelike(int $id): JsonResponse
    {
        $like = $this->likeRepository->find($id);

        if (!$like) {
            return $this->json(['message' => 'like not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($like);
        $this->entityManager->flush();

        return $this->json(['message' => 'like deleted successfully']);
    }
}
