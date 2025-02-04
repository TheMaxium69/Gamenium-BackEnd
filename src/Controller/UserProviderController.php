<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Picture;
use App\Entity\PostActu;
use App\Entity\Provider;
use App\Entity\User;
use App\Entity\UserProvider;
use App\Repository\PostActuRepository;
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
        private ProviderRepository  $providerRepository,
        private PostActuRepository $postActuRepository,
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

            $provider = $this->providerRepository->find($id);

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

    #[Route('/provider/search-postactus', name: 'search_postactus_by_provider', methods: ['POST'])]
    public function searchPostActuByProvider(Request $request): JsonResponse
    {
        //TOKEN
        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);
            //RECUPERATION DU USER
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'no permission']);
            }
            //VERIF DES ROLES USER
            if (!array_intersect(['ROLE_PROVIDER', 'ROLE_PROVIDER_ADMIN'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }
            //VERIFIE LE LIENS AU PROVIDER
            $userProvider = $this->entityManager->getRepository(UserProvider::class)->findOneBy(['user' => $user]);

            if (!$userProvider) {
                return $this->json(['message' => 'no provider']);
            }

            $data = json_decode($request->getContent(), true);
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                return $this->json(['message' => 'Invalid JSON format'], Response::HTTP_BAD_REQUEST);
            }

            $searchValue = $data['searchValue'] ?? '';
            $limit = $data['limit'];

            $results = $this->postActuRepository->searchPostActuByProvider($userProvider->getProvider()->getId(), $searchValue, $limit);

            return $this->json($results, 200, [], ['groups' => 'post:read']);

        } else {
            return $this->json(['message' => 'no token']);
        }

    }

    #[Route('/provider/createPostActu', name: 'create_postactu_provider', methods: ['POST'])]
    public function createPostActuByProvider(Request $request): JsonResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            return $this->json(['message' => 'No token provided'], Response::HTTP_UNAUTHORIZED);
        }

        $token = substr($authorizationHeader, 7);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
        
        if (!$user) {
            return $this->json(['message' => 'Invalid token'], Response::HTTP_UNAUTHORIZED);
        }

        if (!array_intersect(['ROLE_PROVIDER', 'ROLE_PROVIDER_ADMIN'], $user->getRoles())) {
            return $this->json(['message' => 'No permission'], Response::HTTP_FORBIDDEN);
        }

        
        $userProvider = $this->entityManager->getRepository(UserProvider::class)->findOneBy(['user' => $user]);
        if (!$userProvider) {
            return $this->json(['message' => 'No provider linked to user'], Response::HTTP_FORBIDDEN);
        }
        $provider = $userProvider->getProvider();

        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['title'], $data['content'], $data['picture_id'])) {
            return $this->json(['message' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        
        $game = isset($data['game_id']) ? $this->entityManager->getRepository(Game::class)->find($data['game_id']) : null;

        
        $picture = $this->entityManager->getRepository(Picture::class)->find($data['picture_id']);
        if (!$picture) {
            return $this->json(['message' => 'Invalid picture ID'], Response::HTTP_BAD_REQUEST);
        }

        
        $postActu = new PostActu();
        $postActu->setTitle($data['title']);
        $postActu->setContent($data['content']);
        $postActu->setCreatedAt(new \DateTimeImmutable());
        $postActu->setNbEdit(0);
        $postActu->setUser($user);
        $postActu->setIsDeleted(false);
        $postActu->setProvider($provider);
        $postActu->setPicture($picture);
        if ($game) {
            $postActu->setGame($game);
        }
    

        $this->entityManager->persist($postActu);
        $this->entityManager->flush();

        return $this->json(['message' => 'PostActu created successfully', 'result' => $postActu], Response::HTTP_CREATED, [], ['groups' => 'post:read']);
    }

    #[Route('/provider/edit-article/{id}', name: 'update_postactu_provider', methods: ['PUT'])]
    public function updatePostActu(int $id, Request $request): JsonResponse
    {
        $postActu = $this->postActuRepository->find($id);
        if (!$postActu) {
            return $this->json(['message' => 'PostActu not found'], Response::HTTP_NOT_FOUND);
        }

       
        $authorizationHeader = $request->headers->get('Authorization');
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            return $this->json(['message' => 'No token provided'], Response::HTTP_UNAUTHORIZED);
        }
        $token = substr($authorizationHeader, 7);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
        if (!$user) {
            return $this->json(['message' => 'Invalid token'], Response::HTTP_UNAUTHORIZED);
        }

        
        $userRoles = $user->getRoles();
        $isOwner = $postActu->getUser()->getId() === $user->getId();
        $canModifyAll = in_array('PROVIDER', $userRoles) || in_array('PROVIDER_ADMIN', $userRoles);

        if (!$isOwner && !$canModifyAll) {
            return $this->json(['message' => 'You do not have permission to edit this post'], Response::HTTP_FORBIDDEN);
        }

      
        $data = json_decode($request->getContent(), true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format'], Response::HTTP_BAD_REQUEST);
        }

        
        if (isset($data['title'])) $postActu->setTitle($data['title']);
        if (isset($data['content'])) $postActu->setContent($data['content']);
        if (isset($data['game_id'])) {
            $game = $this->entityManager->getRepository(Game::class)->find($data['game_id']);
            if ($game) $postActu->setGame($game);
        }
        if (isset($data['provider_id'])) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($data['provider_id']);
            if ($provider) $postActu->setProvider($provider);
        }
        if (isset($data['picture_id'])) {
            $picture = $this->entityManager->getRepository(Picture::class)->find($data['picture_id']);
            if ($picture) {
                $postActu->setPicture($picture);
            } else {
                return $this->json(['message' => 'Invalid picture ID'], Response::HTTP_BAD_REQUEST);
            }
        }

      
        $postActu->setLastEdit(new \DateTime());
        $postActu->setNbEdit(($postActu->getNbEdit() ?? 0) + 1);

        $this->entityManager->persist($postActu);
        $this->entityManager->flush();

        return $this->json(['message' => 'PostActu updated successfully', 'updated' => $postActu], Response::HTTP_OK, [], ['groups' => 'post:read']);
    }
}
