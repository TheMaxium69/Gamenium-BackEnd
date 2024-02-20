<?php

namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $manager;
    private $user;


public function __construct(EntityManagerInterface $manager, UserRepository $user)
{
    $this->manager = $manager;
    $this->user = $user;
}


    #[Route('/users/', name: 'user_all', methods:"GET")]
    public function getUserAll():JsonResponse
    {
        $users = $this->user->findAll();
        return $this->json($users);
    }

    #[Route('/user/{id}', name: 'user_by_id', methods:"GET")]
    public function getUserById(int $id):JsonResponse
    {
        $user = $this->user->find($id);

        if (!$user) {
            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($user);
    }

    #[Route('/user/', name: 'user_create', methods:"POST")]
    public function createUser (Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['id_useritium']) || !isset($data['userRole']) || !isset($data['ip']) || !isset($data['id_picture'])) {
            return $this->json(['message' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();

        $user->setIdUseritium($data['id_useritium']);
        $user->setUserRole($data['user_role']);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setLastConnection(new \DateTimeImmutable());
        $user->setIp($data['ip']);
        $user->setIdPicture($data['id_picture']);

        $this->manager->persist($user);
        $this->manager->flush();

        return $this->json(['message' => 'User created successfully', 'users' => $user]);
    }

    #[Route('/user/{id}', name: 'user_delete', methods:"DELETE")]
    public function deleteUser(int $id):JsonResponse
    {
        $user = $this->user->find($id);

        if (!$user) {

            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($user);
        $this->manager->flush();

        return $this->json(['message' => 'User deleted successfully']);
    }
}