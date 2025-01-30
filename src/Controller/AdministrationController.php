<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin')]
class AdministrationController extends AbstractController
{

    private $manager;
    private $user;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $manager, UserRepository $user, UserRepository $userRepository)
    {
        $this->manager = $manager;
        $this->user = $user;
        $this->userRepository = $userRepository;
    }


    #[Route('-test', name: 'app_administration')]
    public function test(): JsonResponse
    {

        return $this->json(['message' => 'good']);

    }




    #[Route('-users-search', name: 'search_users_admin', methods: ['POST'])]
    public function searchUsers(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $searchValue = $data['searchValue'] ?? '';
        $limit = $data['limit'];

        $results = $this->userRepository->searchUserByName($searchValue, $limit);

        $finalResults = [];
        foreach ($results as $user) {
            if (!in_array('ROLE_BAN', $user->getRoles())) {
                $finalResults[] = $user;
            }
        }

        return $this->json($finalResults, 200, [], ['groups' => 'useradmin:read']);
    }
}
