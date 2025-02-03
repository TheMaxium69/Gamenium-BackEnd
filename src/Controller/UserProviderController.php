<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Entity\User;
use App\Entity\UserProvider;
use App\Entity\Provider;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserProviderController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProviderRepository  $providerRepository
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
    /* UPDATE */
    #[Route('/provider/{id}', name: 'update_provider', methods: ['PUT'])]
    public function updateProvider(int $id, Request $request): JsonResponse
    {
        $provider = $this->providerRepository->find($id);
        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

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

            $data = json_decode($request->getContent(), true);
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                return $this->json(['message' => 'Invalid JSON format'], Response::HTTP_BAD_REQUEST);
            }

            if (isset($data['displayName'])) $provider->setDisplayName($data['displayName']);
            if (isset($data['content'])) $provider->setContent($data['content']);
            if (isset($data['founded_at'])) $provider->setFoundedAt($data['founded_at']);
            if (isset($data['picture_id'])) {
                $picture = $this->entityManager->getRepository(Picture::class)->find($data['picture_id']);
                if ($picture) {
                    $provider->setPicture($picture);
                } else {
                    return $this->json(['message' => 'Invalid picture ID'], Response::HTTP_BAD_REQUEST);
                }
            }

            $this->entityManager->persist($provider);
            $this->entityManager->flush();

            return $this->json(['message' => 'Provider updated successfully', 'updated' => $provider], Response::HTTP_OK, [], ['groups' => 'provider:read']);
        }
    }


}
