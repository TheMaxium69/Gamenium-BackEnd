<?php

namespace App\Controller;

use App\Entity\Like;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use App\Repository\PostActuRepository;
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
        private UserRepository $userRepository,
        private PostActuRepository $postActuRepository
    ) {}

    #[Route('/likes', name: 'get_all_likes', methods: ['GET'])]
    public function getAlllikes(): JsonResponse
    {
        $likes = $this->likeRepository->findAll();

        return $this->json($likes , 200 , [], ['groups' => 'like:read']);
    }

    #[Route('/like/post-actu/{idPost}', name: 'get_post_actu_likes', methods: ['GET'])]
    public function getPostActuLikes(int $idPost): JsonResponse
    {

        $postActu = $this->postActuRepository->find($idPost);


        if (!$postActu) {
            return $this->json(['error' => 'Post actualité non trouvé']);
        }


        $postActuLikes = $this->likeRepository->findBy(['post' => $postActu]);


        if (empty($postActuLikes)) {
            return $this->json(['message' => 'Aucun like trouvé pour ce post actualité']);
        }


        $likesInfo = [];
        foreach ($postActuLikes as $like) {
            $likeInfo = [
                'id' => $like->getId(),
                'user_id' => $like->getUser()->getId(),
                'ip' => $like->getIp(),
                'created_at' => $like->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
            $likesInfo[] = $likeInfo;
        }

        return $this->json($likesInfo);
    }


    #[Route('/like/comment/{idComment}', name: 'get_comment_likes', methods: ['GET'])]
    public function getCommentLikes(int $idComment): JsonResponse
    {

        $comment = $this->commentRepository->find($idComment);

        if (!$comment) {
            return $this->json(['error' => 'Commentaire non trouvé']);
        }


        $commentLikes = $this->likeRepository->findBy(['comment' => $comment]);

        if (empty($commentLikes)) {
            return $this->json(['message' => 'Aucun like trouvé pour ce commentaire']);
        }


        $likesInfo = [];
        foreach ($commentLikes as $like) {
            $likeInfo = [
                'id' => $like->getId(),
                'user_id' => $like->getUser()->getId(),
                'ip' => $like->getIp(),
                'created_at' => $like->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
            $likesInfo[] = $likeInfo;
        }

        return $this->json($likesInfo);
    }

    #[Route('/like/{id}', name: 'get_like_by_id', methods: ['GET'])]
    public function getlikeById(int $id): JsonResponse
    {
        $like = $this->likeRepository->find($id);

        if (!$like) {
            return $this->json(['message' => 'like not found']);
        }

        return $this->json($like);
    }

        #[Route('/like', name: 'create_like', methods: ['POST'])]
        public function createLike2(Request $request): JsonResponse
        {
            $data = json_decode($request->getContent(), true);


            if (!isset($data['user_id']) || !isset($data['ip']) || (!isset($data['post_id']) && !isset($data['comment_id']))) {
                return $this->json(['error' => 'Les données fournies sont incomplètes'], Response::HTTP_BAD_REQUEST);
            }


            $like = new Like();


            $user = $this->userRepository->find($data['user_id']);
            if (!$user) {
                return $this->json(['error' => 'Utilisateur non trouvé']);
            }


            $like->setUser($user);
            $like->setIp($data['ip']);
            $like->setCreatedAt(new \DateTimeImmutable());


            if (isset($data['post_id'])) {

                $post = $this->postActuRepository->find($data['post_id']);
                if (!$post) {
                    return $this->json(['error' => 'Post non trouvé']);
                }

                $like->setPost($post);
            } elseif (isset($data['comment_id'])) {

                $comment = $this->commentRepository->find($data['comment_id']);
                if (!$comment) {
                    return $this->json(['error' => 'Commentaire non trouvé']);
                }


                $like->setComment($comment);
            }


            $this->entityManager->persist($like);
            $this->entityManager->flush();

        return $this->json(['message' => 'Like créé avec succès'], Response::HTTP_CREATED);
    }

    #[Route('/like/post-actu/{postId}', name: 'delete_post_actu_like', methods: ['DELETE'])]
    public function deletePostActuLike(int $postId, Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);


        $user = $this->userRepository->find($data['user_id']);


        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
        }


        $postActu = $this->postActuRepository->find($postId);


        if (!$postActu) {
            return $this->json(['error' => 'Post actualité non trouvé']);
        }


        $like = $this->likeRepository->findOneBy(['post' => $postActu, 'user' => $user]);


        if (!$like) {
            return $this->json(['error' => 'Like non trouvé pour ce post actualité']);
        }


        $this->entityManager->remove($like);
        $this->entityManager->flush();

        return $this->json(['message' => 'Like sur le post actualité supprimé avec succès']);
    }

    #[Route('/like/comment/{commentId}', name: 'delete_comment_like', methods: ['DELETE'])]
    public function deleteCommentLike(int $commentId, Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);


        $user = $this->userRepository->find($data['user_id']);


        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        $comment = $this->commentRepository->find($commentId);


        if (!$comment) {
            return $this->json(['error' => 'Commentaire non trouvé']);
        }


        $like = $this->likeRepository->findOneBy(['comment' => $comment, 'user' => $user]);


        if (!$like) {
            return $this->json(['error' => 'Like non trouvé pour ce commentaire']);
        }

        $this->entityManager->remove($like);
        $this->entityManager->flush();

        return $this->json(['message' => 'Like sur le commentaire supprimé avec succès']);
    }


}