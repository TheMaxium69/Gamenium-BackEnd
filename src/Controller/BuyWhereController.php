<?php

namespace App\Controller;

use App\Entity\BuyWhere;
use App\Entity\User;
use App\Repository\BuyWhereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuyWhereController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BuyWhereRepository $buywhere
    ) {}

    #[Route('/buywheres', name: 'all_buywhere', methods:"GET")]
    public function getAllBuyWheres():JSONResponse
    {
        $buywherePublic = $this->buywhere->findBy(['is_public' => true]);
        return $this->json(['message' => 'good', 'result' => $buywherePublic], 200, [], ['groups' => 'buywhere:read']);
    }

    #[Route('/buywherebyuser', name: 'all_buywhere_by_user', methods:"GET")]
    public function getAllBuyWheresByUser(Request $request):JSONResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /* SI L'UTILISATEUR CORRESPOND AU TOKEN  */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'Token invalide']);
            }

            $buywhereUser = $this->buywhere->findBy(['user' => $user]);
        }
        return $this->json(['message' => 'good', 'result' => $buywhereUser], 200, [], ['groups' => 'buywhere:read']);
    }

    #[Route('/buywhere/{id}', name: 'buywere_by_id', methods:"GET")]
    public function getOneBuyWhere(int $id): JSONResponse
    {
        $buywhereOne = $this->buywhere->find($id);
        return $this->json(['message' => 'good', 'result' => $buywhereOne], 200, [], ['groups' => 'buywhere:read']);
    }


}

