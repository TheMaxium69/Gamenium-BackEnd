<?php

namespace App\Controller;

use App\Entity\Like;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
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
        private LikeRepository $likeRepository,
        private CommentRepository $commentRepository,
        private UserRepository $userRepository
    ) {}

    #[Route('/likes', name: 'get_all_likes', methods: ['GET'])]
    public function getAlllikes(): JsonResponse
    {
        $likes = $this->likeRepository->findAll();

        return $this->json($likes , 200 , [], ['groups' => 'like:read']);
    }

    #[Route('/like/post-actu/{idPost}', name: 'get_likes_count', methods: ['GET'])]
    public function getLikesCount(int $idPost): JsonResponse
    {

        $likesCount = $this->likeRepository->count(['post' => $idPost]);


        $likedUsers = $this->likeRepository->findBy(['post' => $idPost]);

        $response = [
            'message' => 'good',
            'total' => $likesCount,
            'result' => array_map(fn($like) => ['id' => $like->getUser()->getId()], $likedUsers)
        ];

        // Retourner la réponse JSON
        return $this->json($response);
    }

    #[Route('/like/comment/{idComment}', name: 'get_likes_count_by_comment', methods: ['GET'])]
    public function getLikesCountByComment(int $idComment): JsonResponse
    {
        $likesCount = $this->likeRepository->count(['comment' => $idComment]);

        $likedUsers = $this->likeRepository->findBy(['comment' => $idComment]);

        $response = [
            'message' => 'good',
            'total' => $likesCount,
            'result' => array_map(fn($like) => ['id' => $like->getUser()->getId()], $likedUsers)
        ];

        return $this->json($response);
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




    #[Route('/like/post-actu/{idPost}', name: 'get_likes_count', methods: ['GET'])]
    public function LikesCount(int $idPost): JsonResponse
    {
        $likesCount = $this->likeRepository->count(['post' => $idPost]);

        $likedUsers = $this->likeRepository->findBy(['post' => $idPost]);

        $response = [
            'message' => 'good',
            'total' => $likesCount,
            'result' => array_map(fn($like) => ['id' => $like->getUser()->getId()], $likedUsers)
        ];

        return $this->json($response);
    }


    #[Route('/like/comment/{idComment}', name: 'get_likes_count_by_comment', methods: ['GET'])]
    public function LikesCountByComment(int $idComment): JsonResponse
    {
        $likesCount = $this->likeRepository->count(['comment' => $idComment]);

        $likedUsers = $this->likeRepository->findBy(['comment' => $idComment]);

        $response = [
            'message' => 'good',
            'total' => $likesCount,
            'result' => array_map(fn($like) => ['id' => $like->getUser()->getId()], $likedUsers)
        ];

        return $this->json($response);
    }



    #[Route('/like/comment/{idComment}', name: 'add_like_to_comment', methods: ['POST'])]
public function addLikeToComment(int $idComment, Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['user_id'])) {
        return $this->json(['error' => 'Les données fournies sont incomplètes'], Response::HTTP_BAD_REQUEST);
    }

    $comment = $this->commentRepository->find($idComment);

    if (!$comment) {
        return $this->json(['error' => 'Commentaire non trouvé'], Response::HTTP_NOT_FOUND);
    }

    $like = new Like();
    $like->setUser($this->userRepository->find($data['user_id']));
    $like->setComment($comment);
    $like->setIp($data['ip']);
    $like->setCreatedAt(new \DateTimeImmutable());

    $this->entityManager->persist($like);
    $this->entityManager->flush();

    return $this->json(['message' => 'Like ajouté avec succès au commentaire'], Response::HTTP_CREATED);
}
    }