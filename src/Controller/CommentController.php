<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\PostActu;
use App\Entity\User;
use App\Repository\CommentReplyRepository;
use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CommentController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private CommentRepository $commentRepository,
        private CommentReplyRepository $commentReplyRepository,
        private UserRepository $userRepository,
        private LikeRepository $likeRepository
    ) {}

    #[Route('/countCommentByActu/{id}', name: 'comment_count', methods:"GET")]
    public function countComByActu(int $id): JsonResponse
    {
        $totalcom = 0;
        $commentReply = [];

        $actu = $this->entityManager->getRepository(PostActu::class)->find($id);
        if (!$actu){
            return $this->json(['message' => 'actuality not found']);
        }

        $comment = $this->commentRepository->findBy(['post' => $actu]);
        if (!$comment){
            return $this->json(['message' => 'good', 'result' => [
                'total' => 0,
                'reply' => 0
            ]]);
        }

        $totalcom = $totalcom + count($comment);

        foreach ($comment as $com) {
            $tempComReply = null;
            $tempComReply = $this->commentReplyRepository->findBy(['comment' => $com]);
            if ($tempComReply){
                $commentReply[$com->getId()] = $tempComReply;
            }
            $totalcom = $totalcom + count($tempComReply);
        }

        return $this->json(['message' => 'good', 'result' => [
            'total' => $totalcom,
            'reply' => $commentReply,
        ]], 200, [], ['groups' => 'commentreply:read']);
    }


    #[Route('/getCommentByActu/{id}', name: 'comment_by_actu', methods:"GET")]
    public function getCommentByActu(int $id):JsonResponse
    {

        $post = $this->entityManager->getRepository(PostActu::class)->find($id);

        if (!$post){

            return $this->json(['message' => 'actuality not found']);

        } else {

            $CommentMyPost = $this->commentRepository->findBy(['post' => $post]);

            $commentAll = [];
            foreach ($CommentMyPost as $comment) {
                $commentAll[] = $comment;
            }

            if ($commentAll == []){

                $message = [
                    'message' => "aucun commentaire"
                ];

            } else {

                $message = [
                    'message' => "good",
                    'result' => $commentAll
                ];

            }

            return $this->json($message, 200, [], ['groups' => 'comment:read']);
        }

    }

    #[Route('comments/me', name: 'get_user_comments', methods: "GET" )]
    public function getCommentByUser (Request $request) : JsonResponse {

        // récup l'utilisateur via token
        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer') === 0) {
            $token = substr($authorizationHeader, 7);
        

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {

                return $this->json(['message' => 'Token non trouvé']);

            } 


            // Récup tout les like du user 

            $userComments = $this->commentRepository->findBy(['user' => $user]);

            // préparer la reponse 

            $message = [
                'message' => 'good',
                'result' => $userComments,
            ];

            return $this->json($message, 200, [], ['groups' => 'comment:read']);

        }

        return $this->json(['message' => 'No token']);
        
    }

    #[Route('/commentInActu/', name: 'comment_create', methods:"POST")]
    public function createCommentInActu (Request $request):JsonResponse{
        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['content']) || !isset($data['id_post'])){
            return $this->json(['message' => 'undefine of field']);
        }

        /* MET UNE LIMITE DE TAILLE DU COMMENTAIRE */
        if (strlen(($data['content'])) > 300) {
            return $this->json(['message' => 'to long content']);
        }

        /* SET UNE IP */
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
        }

        $idActu = $data['id_post'];
        $content = $data['content'];


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
            $actu = $this->entityManager->getRepository(PostActu::class)->findOneBy(['id' => $idActu]);
            if (!$actu){
                return $this->json(['message' => 'actuality is failed']);
            }

            $comment = new Comment();
            $comment ->setContent($content);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUser($user);
            $comment->setPost($actu);
            $comment->setIp($newIp);
            $comment->setLastEdit(new \DateTime());
            $comment->setNbEdit(0);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $comment], 200, [], ['groups' => 'comment:read']);

        }

        return $this->json(['message' => 'no token']);

    }

    #[Route('/comment/{id}', name: 'comment_delete', methods:"DELETE")]
    public function deleteComment(int $id, Request $request):JsonResponse{

        if (!$id) {
            return $this->json(['message' => 'no id']);
        }

        $comment = $this->commentRepository->find($id);

        if (!$comment) {
            
            return $this->json(['message' => 'Comment not found'], 200);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'token is failed']);
            }

            if ($comment->getUser()->getId() != $user->getId()) {
                return $this->json(['message' => 'You have no permission']);
            }

            $allLikes = $this->likeRepository->findBy(['comment' => $comment]);

            foreach ($allLikes as $like) {
                $like->setComment(null);
            }


            $allReply = $this->commentReplyRepository->findBy(['comment' => $comment]);
            foreach ($allReply as $reply) {
                $reply->setComment(null);
            }

            $this->entityManager->remove($comment);
            $this->entityManager->flush();

            return $this->json(['message' => 'Comment deleted successfully']);
        } else {
            return $this->json(['message' => 'no token']);
        }
    }
}
