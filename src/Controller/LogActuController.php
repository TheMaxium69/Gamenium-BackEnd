<?php

namespace App\Controller;

use App\Entity\LogActu;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogActuController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}


    #[Route('/log-actu-view', name: 'app_log_actu', methods: ['POST'])]
    public function logActuView(Request $request): JsonResponse
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

            $results = $this->entityManager->getRepository(LogActu::class)->searchLogByAction($searchValue, $limit);

            return $this->json($results, 200, [], ['groups' => 'logactu:read']);


        } else {

            return $this->json(['message' => 'no permission']);

        }

    }
}
