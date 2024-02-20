<?php

namespace App\Controller;

use App\Entity\NoteUser;
use App\Repository\NoteUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoteUserController extends AbstractController
{
    private $manager;
    private $noteuser;

    public function __construct(EntityManagerInterface $manager, NoteUserRepository $noteuser)
    {
        $this->manager = $manager;
        $this->noteuser = $noteuser;
    }

    #[Route('/noteusers/', name: 'all_note_user', methods:"GET")]
    
    public function getUserNoteAll():JSONResponse
    {
        $noteuser = $this->noteuser->findAll();
        return $this->json($noteuser);
    }

    #[Route('/noteuser/{id}', name: 'noteuser_by_id', methods:"GET")]

    public function getNoteUserById(int $id):JsonResponse
    {
        $noteuser = $this->noteuser->find($id);
        return $this->json($noteuser);
    }

    #[Route('/noteuser', name: 'create_note_user', methods: ['POST'])]

    public function createNoteUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $noteuser = new NoteUser();
        $noteuser->setIdUser($data['id_user']);
        $noteuser->setIdGame($data['id_game']);
        $noteuser->setRating($data['rating']);
        $noteuser->setCreatedAt(new \DateTime());
        $noteuser->setIp($data['ip']);

        $this->manager->persist($noteuser);
        $this->manager->flush();

        return $this->json(['message' => 'Note User created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/noteuser/{id}', name: 'note_user_delete', methods: ['DELETE'])]

    public function deleteNoteUser(int $id):JsonResponse
    {
        $noteuser=$this->noteuser->find($id);

        if(!$noteuser) {
            return $this->json(['message' => 'Note User not found'], Response::HTTP_NOT_FOUND );
        }

        $this->manager->remove($noteuser);
        $this->manager->flush();

        return $this->json(['message' => 'Note User deleted successfully']);
    }
}
