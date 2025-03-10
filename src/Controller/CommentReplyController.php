<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentReply;
use App\Entity\LogComment;
use App\Entity\PostActu;
use App\Entity\User;
use App\Repository\CommentReplyRepository;
use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CommentReplyController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private CommentReplyRepository $commentReplyRepository
    ) {}

    #[Route('/comment-reply-create/', name: 'app_comment_reply_create', methods: ['POST'])]
    public function createReply(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['content']) || !isset($data['id_comment'])){
            return $this->json(['message' => 'undefine of field']);
        }

        /* MET UNE LIMITE DE TAILLE DU COMMENTAIRE */
        if (strlen(($data['content'])) > 255) {
            return $this->json(['message' => 'to long content']);
        }

        /* SET UNE IP */
        $ip = $request->getClientIp();
        if (!isset($ip)) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $ip;
        }

        $idComment = $data['id_comment'];
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

            /*SI L'COMMENT EXISTE*/
            $comment = $this->entityManager->getRepository(Comment::class)->findOneBy(['id' => $idComment]);
            if (!$comment){
                return $this->json(['message' => 'comment is failed']);
            }

            $commentReply = new CommentReply();
            $commentReply ->setContent($content);
            $commentReply->setCreatedAt(new \DateTimeImmutable());
            $commentReply->setUser($user);
            $commentReply->setComment($comment);
            $commentReply->setIp($newIp);
            $commentReply->setLastEdit(new \DateTime());
            $commentReply->setNbEdit(0);
            $commentReply->setIsDeleted(false);

            $this->entityManager->persist($commentReply);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $commentReply], 200, [], ['groups' => 'commentreply:read']);

        }

        return $this->json(['message' => 'no token']);

    }


    #[Route('/getReplyByComment/{id}', name: 'app_reply_by_comment')]
    public function getReplyByComment(int $id): JsonResponse
    {

        $commentReply = $this->entityManager->getRepository(CommentReply::class)->findBy(['comment' => $id]);

        $commentReplyFinal = [];
        foreach ($commentReply as $commentReplyOne){
            if ($commentReplyOne->getIsDeleted()){
                $commentReplyFinal[] = $commentReplyOne;
            }

        }

        return $this->json(['message' => 'good', 'result' => $commentReplyFinal], 200, [], ['groups' => 'commentreply:read']);

    }

    #[Route('/getReplyById/{id}', name: 'app_reply_by_id')]
    public function getReplyById(Request $request, int $id): JsonResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');

        $commentReply = $this->commentReplyRepository->find($id);
        if(!$commentReply) {
            return $this->json(['message' => 'Comment reply not found']);
        }

        //On vérifie que le token n'est pas vide
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            
            //on verifie que le user existe
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            //on vérifie que le user a bien le role Administrateur
            if (!in_array('ROLE_OWNER', $user->getRoles()) &&
            !in_array('ROLE_ADMIN', $user->getRoles()) && 
            !in_array('ROLE_MODO_RESPONSABLE', $user->getRoles()) && 
            !in_array('ROLE_MODO_SUPER', $user->getRoles()) && 
            !in_array('ROLE_MODO', $user->getRoles()))
            {
                return $this->json(['message' => 'no permission']);
            }
           
            return $this->json(['message' => 'good', 'result' => $commentReply], 200, [], ['groups' => 'commentreply:admin']);
        }

        return $this->json(['message' => 'Token invalide']);

    }

    #[Route('/deleteReply/{id}', name: 'app_delete_reply', methods: ['DELETE'])]
    public function deleteReply(int $id, Request $request): JsonResponse
    {

        $commentReply = $this->entityManager->getRepository(CommentReply::class)->findOneBy(['id' => $id]);

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'token is failed']);
            }

            if ($commentReply->getUser()->getId() != $user->getId()){
                return $this->json(['message' => 'no have permission']);
            }

            /* LOG */
            $newLogComment = new LogComment();
            $newLogComment->setUser($commentReply->getUser());
            $newLogComment->setContent($commentReply->getContent());
            $newLogComment->setCreatedAt($commentReply->getCreatedAt());
            $newLogComment->setDeletedAt(new \DateTimeImmutable());
            /* LOG */

            $this->entityManager->remove($commentReply);
            $this->entityManager->flush();

            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }

    }







}
