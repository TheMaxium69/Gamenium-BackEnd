<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostActuRepository;
use App\Repository\UserRepository;
use App\Repository\ViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Amp\Iterator\toArray;

#[Route('admin')]
class AdministrationController extends AbstractController
{


    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}



    #[Route('-users-search', name: 'search_users_admin', methods: ['POST'])]
    public function searchUsers(Request $request): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'no permission']);
            }

            if (!in_array('ROLE_ADMIN', $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }
            

            $data = json_decode($request->getContent(), true);
            $searchValue = $data['searchValue'] ?? '';
            $limit = $data['limit'];

            $results = $this->entityManager->getRepository(User::class)->searchUserByName($searchValue, $limit);

            return $this->json($results, 200, [], ['groups' => 'useradmin:read']);
            
            
            

        } else {

            return $this->json(['message' => 'no permission']);

        }

    }
}
