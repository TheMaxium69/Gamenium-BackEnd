<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\PostActu;
use App\Entity\User;
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

    // VIEW LIKE IN POSTE
    #[Route('/like/post-actu/{idPost}', name: 'get_post_actu_likes', methods: ['GET'])]
    public function getPostActuLikes(int $idPost): JsonResponse
    {

        $postActu = $this->postActuRepository->find($idPost);


        if (!$postActu) {
            return $this->json(['message' => 'post-actu undefine']);
        }


        $postActuLikes = $this->likeRepository->findBy(['post' => $postActu]);


        if (empty($postActuLikes)) {
            $message = [
                "message" => "good",
                "result" => []
            ];
            return $this->json($message);
        }

        $message = [
            "message" => "good",
            "result" => $postActuLikes,
        ];

        return $this->json($message, 200, [], ['groups' => 'like:read'] );
    }


    // VIEW LIKE IN COMMENT
    #[Route('/like/comment/{idComment}', name: 'get_comment_likes', methods: ['GET'])]
    public function getCommentLikes(int $idComment): JsonResponse
    {

        $comment = $this->commentRepository->find($idComment);

        if (!$comment) {
            return $this->json(['message' => 'comment undefine']);
        }

        $commentLikes = $this->likeRepository->findBy(['comment' => $comment]);


        if (empty($commentLikes)) {
            $message = [
                "message" => "good",
                "result" => []
            ];
            return $this->json($message);
        }

        $message = [
            "message" => "good",
            "result" => $commentLikes,
        ];

        return $this->json($message, 200, [], ['groups' => 'like:read'] );
    }


    // ADD LIKE IN POSTE
    #[Route('/like/post-actu/', name: 'add_like_post_actu', methods: ['POST'])]
    public function addLikePostActu(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id_postactu'])){
            return $this->json(['message' => 'undefine of field']);
        }

        /* SET UNE IP */
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
        }

        $idPostActu = $data['id_postactu'];

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            /*SI L'ACTU EXISTE*/
            $postActu = $this->entityManager->getRepository(PostActu::class)->findOneBy(['id' => $idPostActu]);
            if (!$postActu){
                return $this->json(['message' => 'actu is failed']);
            }

            /*SI IL EST DJA FOLLOW*/
            $isLike = $this->likeRepository->findOneBy(['post' => $postActu, 'user'=>$user]);
            if($isLike){
                return $this->json(['message' => 'user as liked']);
            }

            $like = new Like();
            $like->setPost($postActu);
            $like->setUser($user);
            $like->setIp($newIp);
            $like->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($like);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $like], 200, [], ['groups' => 'like:read']);

        }

        return $this->json(['message' => 'no token']);

    }

    // ADD LIKE IN COMMENT
    #[Route('/like/comment/', name: 'add_like_comment', methods: ['POST'])]
    public function addLikeComment(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id_comment'])){
            return $this->json(['message' => 'undefine of field']);
        }

        /* SET UNE IP */
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
        }

        $idComment = $data['id_comment'];

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            /*SI L'ACTU EXISTE*/
            $comment = $this->entityManager->getRepository(Comment::class)->findOneBy(['id' => $idComment]);
            if (!$comment){
                return $this->json(['message' => 'comment is failed']);
            }

            /*SI IL EST DJA FOLLOW*/
            $isLike = $this->likeRepository->findOneBy(['comment' => $comment, 'user'=>$user]);
            if($isLike){
                return $this->json(['message' => 'user as liked']);
            }

            $like = new Like();
            $like->setComment($comment);
            $like->setUser($user);
            $like->setIp($newIp);
            $like->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($like);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $like], 200, [], ['groups' => 'like:read']);

        }

        return $this->json(['message' => 'no token']);

    }

}