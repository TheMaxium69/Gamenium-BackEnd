<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserProviderController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}





    #[Route('/getProvider/', name: 'app_user_provider')]
    public function getProvider(Request $request): JsonResponse
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

            if (!array_intersect(['ROLE_PROVIDER', 'ROLE_PROVIDER_ADMIN'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }


            $userProvider = $this->entityManager->getRepository(UserProvider::class)->findOneBy(['user' => $user]);

            if (!$userProvider) {
                return $this->json(['message' => 'no provider']);
            }


            return $this->json(['message' => 'good', 'result' => $userProvider->getProvider()], 200, [], ['groups' => 'provider:read']);



        } else {
            return $this->json(['message' => 'no token']);
        }



    }
}
