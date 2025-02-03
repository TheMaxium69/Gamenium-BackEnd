<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentReply;
use App\Entity\Log;
use App\Entity\Picture;
use App\Entity\PostActu;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('modo')]
class ModerationController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('-exemple', name: 'app_moderation')]
    public function exemple(Request $request): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderated = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderated) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderated->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }





            /* FOR LOG */
            $newLog = new Log();
            $newLog->setWhy("BAN USER");
            $newLog->setUser(/* SET L'UTILISATEUR CONCERNER */);
            $newLog->setModeratedBy($moderated);
            $newLog->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($newLog);
            $this->entityManager->flush();
            /* FOR LOG */





            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }

    }

    #[Route('-comment', name: 'app_moderation-comment', methods:['POST'])]
    public function moderateDeleteComment(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        if (!isset($data['comment_id'])) {
            return $this->json(['message' => 'undefine of field']);
        }

        $comment = $this->entityManager->getRepository(Comment::class)->find(['id' => $data['comment_id']]);

        if(!$comment) {
            return $this->json(['message' => 'comment not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderated = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderated) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderated->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $user = $comment->getUser();

            /* FOR LOG */
            $newLog = new Log();
            $newLog->setWhy("COMMENT DELETE");
            $newLog->setUser($user);
            $newLog->setModeratedBy($moderated);
            $newLog->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($newLog);
            $this->entityManager->flush();
            /* FOR LOG */

            $comment->setIsDeleted(true);
            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }

    }
    
    #[Route('-comment-reply', name: 'app_moderation-comment-reply', methods:['POST'])]
    public function moderateDeleteCommentReply(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        if (!isset($data['comment_reply_id'])) {
            return $this->json(['message' => 'undefine of field']);
        }

        $commentReply = $this->entityManager->getRepository(CommentReply::class)->find(['id' => $data['comment_reply_id']]);

        if(!$commentReply) {
            return $this->json(['message' => 'Reply not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderated = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderated) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderated->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $user = $commentReply->getUser();

            /* FOR LOG */
            $newLog = new Log();
            $newLog->setWhy("COMMENT DELETE");
            $newLog->setUser($user);
            $newLog->setModeratedBy($moderated);
            $newLog->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($newLog);
            $this->entityManager->flush();
            /* FOR LOG */

            $commentReply->setIsDeleted(true);
            $this->entityManager->persist($commentReply);
            $this->entityManager->flush();

            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }

    }

    #[Route('-actu', name: 'app_moderation-actu', methods:['POST'])]
    public function moderateDeleteActu(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        if (!isset($data['actu_id'])) {
            return $this->json(['message' => 'undefine of field']);
        }

        $actu = $this->entityManager->getRepository(PostActu::class)->find(['id' => $data['actu_id']]);

        if(!$actu) {
            return $this->json(['message' => 'Actu not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderated = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderated) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderated->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $user = $actu->getUser();

            /* FOR LOG */
            $newLog = new Log();
            $newLog->setWhy("ACTU DELETE");
            $newLog->setUser($user);
            $newLog->setModeratedBy($moderated);
            $newLog->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($newLog);
            $this->entityManager->flush();
            /* FOR LOG */

            $actu->setIsDeleted(true);
            $this->entityManager->persist($actu);
            $this->entityManager->flush();

            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }

    }
    
    #[Route('-pp', name: 'app_moderation-pp', methods:['POST'])]
    public function moderateDeletePP(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        if (!isset($data['profil_id'])) {
            return $this->json(['message' => 'undefine of field']);
        }

        $user = $this->entityManager->getRepository(User::class)->find(['id' => $data['profil_id']]);

        if(!$user) {
            return $this->json(['message' => 'user not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderated = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderated) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderated->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $picture = $user->getPp();

            /* FOR LOG */
            $newLog = new Log();
            $newLog->setWhy("PP DELETE");
            $newLog->setUser($user);
            $newLog->setModeratedBy($moderated);
            $newLog->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($newLog);
            $this->entityManager->flush();
            /* FOR LOG */

            $picture->setIsDeleted(true);
            $this->entityManager->persist($picture);
            $this->entityManager->flush();

            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }

    }
}
