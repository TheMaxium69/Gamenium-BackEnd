<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentReply;
use App\Entity\PostActu;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CommentReplyController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager
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
        if (strlen(($data['content'])) > 300) {
            return $this->json(['message' => 'to long content']);
        }

        /* SET UNE IP */
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
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
            $comment = $this->entityManager->getRepository(PostActu::class)->findOneBy(['id' => $idComment]);
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

            $this->entityManager->persist($commentReply);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $commentReply], 200, [], ['groups' => 'commentreply:read']);

        }

        return $this->json(['message' => 'no token']);

    }
}