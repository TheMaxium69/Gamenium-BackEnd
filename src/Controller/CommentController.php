<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{

    private $manager;
    private $picture;

    public function __construct(EntityManagerInterface $manager, PictureRepository $picture)
    {
        $this->manager = $manager;
        $this->comment = $comment;
    }

    #[Route('/comment/', name: 'comment_all', methods:"GET")]
    public function getCommentByAll():JsonResponse{

        $comment = $this->comment->findAll();
        return $this->json($comment);
    }

    #[Route('/comment/{id}', name: 'comment_by_id', methods:"GET")]
    public function getCommentById(int $id):JsonResponse{

        $comment = $this->comment->find($id);

        if (!$comment) {
            return $this->json(['message' => 'Comment not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($comment);
    }

    #[Route('/comment/', name: 'comment_create', methods:"POST")]
    public function createComment (Request $request):JsonResponse{

        $data = json_decode($request->getContent(), true);

        $comment = new Comment();
        $comment->setContent($data['content']);
        $comment->setCreatedAt(new \DateTimeImmutable());
        $comment->setIdPost($data['id_post']);
        $comment->setIdUser($data['id_user']);
        $comment->setIp($data['ip']);
        $comment->setLastEdit(new \DateTimeImmutable());
        $comment->setNbEdit(0);

        $this->manager->persist($comment);
        $this->manager->flush();

        return $this->json('Comment created successfully', Response::HTTP_CREATED);
    }

    #[Route('/comment/{id}', name: 'comment_delete', methods:"DELETE")]
    public function deleteComment(int $id):JsonResponse{

        $comment = $this->comment->find($id);

        if (!$comment) {
            
            return $this->json(['message' => 'Comment not found'], Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($comment);
        $this->manager->flush();

        return $this->json('Comment deleted successfully');
    }
}
