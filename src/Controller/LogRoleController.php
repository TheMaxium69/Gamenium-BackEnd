<?php

namespace App\Controller;

use App\Entity\LogRole;
use App\Entity\User;
use App\Repository\CommentReplyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogRoleController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}


    #[Route('/log-role-view', name: 'app_log_role')]
    public function logroleview(Request $request): JsonResponse
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

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }


            $data = json_decode($request->getContent(), true);
            $searchValue = $data['searchValue'] ?? '';
            $limit = $data['limit'];

            $results = $this->entityManager->getRepository(LogRole::class)->searchLogByName($searchValue, $limit);

            return $this->json($results, 200, [], ['groups' => 'logrole:read']);


        } else {

            return $this->json(['message' => 'no permission']);

        }

    }
}
