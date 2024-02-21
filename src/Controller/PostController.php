<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PostRepository $postRepository
    ) {}

    #[Route('/posts', name: 'get_all_posts', methods: ['GET'])]
    public function getAllPosts(): JsonResponse
    {
        $posts = $this->postRepository->findAll();

        return $this->json($posts);
    }

    #[Route('/posts/{id}', name: 'get_post_by_id', methods: ['GET'])]
    public function getPostById(int $id): JsonResponse
    {
        $post = $this->postRepository->find($id);

        if (!$post) {
            return $this->json(['message' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($post);
    }

    #[Route('/posts', name: 'create_post', methods: ['POST'])]
    public function createPost(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $post = new Post();
        $post->setCreatedAt(new \DateTimeImmutable($data['created_at']));
        $post->setIdProvider($data['id_provider']);
        $post->setIdGameActuality($data['id_game_actuality']);
        $post->setIdUser($data['id_user']);
        $post->setIp($data['ip']);
        $post->setLastEdit(new \DateTime($data['last_edit']));
        $post->setNbEdit($data['nb_edit']);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $this->json(['message' => 'Post created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/posts/{id}', name: 'delete_post', methods: ['DELETE'])]
    public function deletePost(int $id): JsonResponse
    {
        $post = $this->postRepository->find($id);

        if (!$post) {
            return $this->json(['message' => 'Post not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($post);
        $this->entityManager->flush();

        return $this->json(['message' => 'Post deleted successfully']);
    }
}
