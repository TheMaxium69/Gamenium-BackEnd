<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\LogActu;
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
                return $this->json(['message' => 'Invalid JSON format']);
            }

            if (isset($data['displayName'])) $provider->setDisplayName($data['displayName']);
            if (isset($data['content'])) $provider->setContent($data['content']);
            if (isset($data['founded_at'])) $provider->setFoundedAt($data['founded_at']);
            if (isset($data['picture_id'])) {
                $picture = $this->entityManager->getRepository(Picture::class)->find($data['picture_id']);
                if ($picture) {
                    $provider->setPicture($picture);
                } else {
                    return $this->json(['message' => 'Invalid picture ID']);
                }
            }

            $this->entityManager->persist($provider);
            $this->entityManager->flush();

            return $this->json(['message' => 'Provider updated successfully', 'updated' => $provider], 200, [], ['groups' => 'provider:read']);
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
                return $this->json(['message' => 'Invalid JSON format']);
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
            return $this->json(['message' => 'No token provided']);
        }

        $token = substr($authorizationHeader, 7);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
        
        if (!$user) {
            return $this->json(['message' => 'Invalid token']);
        }

        if (!array_intersect(['ROLE_PROVIDER', 'ROLE_PROVIDER_ADMIN'], $user->getRoles())) {
            return $this->json(['message' => 'No permission']);
        }

        
        $userProvider = $this->entityManager->getRepository(UserProvider::class)->findOneBy(['user' => $user]);
        if (!$userProvider) {
            return $this->json(['message' => 'No provider linked to user']);
        }
        $provider = $userProvider->getProvider();

        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['title'], $data['content'], $data['picture_id'])) {
            return $this->json(['message' => 'Missing required fields']);
        }

        
        $game = isset($data['game_id']) ? $this->entityManager->getRepository(Game::class)->find($data['game_id']) : null;

        
        $picture = $this->entityManager->getRepository(Picture::class)->find($data['picture_id']);
        if (!$picture) {
            return $this->json(['message' => 'Invalid picture ID']);
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

        $logActu = new LogActu();
        $logActu->setUser($user);  
        $logActu->setActu($postActu);  
        $logActu->setAction("CREATE");
        $logActu->setRoute("PROVIDER");
        $logActu->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($logActu);

        $this->entityManager->flush();

        return $this->json(['message' => 'PostActu created successfully', 'result' => $postActu], 200, [], ['groups' => 'post:read']);
    }

    #[Route('/provider/edit-article/{id}', name: 'update_postactu_provider', methods: ['PUT'])]
    public function updatePostActu(int $id, Request $request): JsonResponse
    {
        $postActu = $this->postActuRepository->find($id);
        if (!$postActu) {
            return $this->json(['message' => 'PostActu not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            return $this->json(['message' => 'No token provided']);
        }

        $token = substr($authorizationHeader, 7);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
        if (!$user) {
            return $this->json(['message' => 'Invalid token']);
        }

        
        $userProvider = $this->entityManager->getRepository(UserProvider::class)
            ->findOneBy(['user' => $user, 'provider' => $postActu->getProvider()]);

        if (!$userProvider) {
            return $this->json(['message' => 'You are no longer linked to this provider. Modification denied.']);
        }

        $userRoles = $user->getRoles();
        $isOwner = $postActu->getUser()->getId() === $user->getId();
        $canModifyAll = in_array('ROLE_PROVIDER', $userRoles) || in_array('ROLE_PROVIDER_ADMIN', $userRoles);

        if (!$isOwner && !$canModifyAll) {
            return $this->json(['message' => 'You do not have permission to edit this post']);
        }

        $data = json_decode($request->getContent(), true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
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
                return $this->json(['message' => 'Invalid picture ID']);
            }
        }

        $postActu->setLastEdit(new \DateTime());
        $postActu->setNbEdit(($postActu->getNbEdit() ?? 0) + 1);

        $this->entityManager->persist($postActu);

        /* LOG */
        $logActu = new LogActu();
        $logActu->setUser($user);
        $logActu->setActu($postActu);
        $logActu->setAction("EDIT");
        $logActu->setRoute("PROVIDER");
        $logActu->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($logActu);

        $this->entityManager->flush();

        return $this->json(['message' => 'PostActu updated successfully', 'updated' => $postActu], 200, [], ['groups' => 'post:read']);
    }


    #[Route('/provider/deletePostActu/{id}', name: 'delete_postactu_provider', methods: ['PUT'])]
    public function deletePostActuByProvider(int $id, Request $request): JsonResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            return $this->json(['message' => 'No token provided']);
        }

        $token = substr($authorizationHeader, 7);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            return $this->json(['message' => 'Invalid token']);
        }

        if (!array_intersect(['ROLE_PROVIDER_ADMIN', 'ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $user->getRoles())) {
            return $this->json(['message' => 'No permission']);
        }

        $postActu = $this->entityManager->getRepository(PostActu::class)->find($id);
        if (!$postActu) {
            return $this->json(['message' => 'PostActu not found']);
        }

        $userProvider = $this->entityManager->getRepository(UserProvider::class)->findOneBy(['user' => $user]);
        if (!$userProvider || $postActu->getProvider()->getId() !== $userProvider->getProvider()->getId()) {
            return $this->json(['message' => 'This post does not belong to your provider']);
        }

        $postActu->setIsDeleted(true);
        $this->entityManager->persist($postActu);

        $logActu = new LogActu();
        $logActu->setUser($user);
        $logActu->setActu($postActu); 
        $logActu->setAction("DELETE");
        $logActu->setRoute("PROVIDER");
        $logActu->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($logActu);

        $this->entityManager->flush();

        return $this->json(['message' => 'PostActu marked as deleted successfully']);
    }

    //Crée -> Link User Provider
    #[Route('/user-provider/link', name: 'link_user_provider', methods: ['POST'])]
    public function linkUserToProvider(Request $request): JsonResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'Invalid token']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
                return $this->json(['message' => 'No permission']);
            }

            $data = json_decode($request->getContent(), true);
            if (!isset($data['user_id'], $data['provider_id'])) {
                return $this->json(['message' => 'Champ manquant']);
            }

            $targetUser = $this->entityManager->getRepository(User::class)->find($data['user_id']);
            $provider = $this->entityManager->getRepository(Provider::class)->find($data['provider_id']);

            if (!$targetUser || !$provider) {
                return $this->json(['message' => 'Utilisateur ou Provider non trouver']);
            }

            // Vérifier si l'association existe déjà
            $existingLink = $this->entityManager->getRepository(UserProvider::class)->findOneBy(['user' => $targetUser]);
            if ($existingLink) {
                return $this->json(['message' => 'Utilisateur déjà relié à ce provider']);
            }

            // Créer l'association
            $userProvider = new UserProvider();
            $userProvider->setUser($targetUser);
            $userProvider->setProvider($provider);
            $this->entityManager->persist($userProvider);
            $this->entityManager->flush();

            return $this->json(['message' => 'good'], Response::HTTP_CREATED);
        }

        return $this->json(['message' => 'No token'], Response::HTTP_UNAUTHORIZED);
    }

    // Récup tout les liens existants
    #[Route('/admin/user-providers', name: 'get_user_providers', methods: ['GET'])]
    public function getUserProviders(Request $request): JsonResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            return $this->json(['message' => 'No token provided']);
        }

        $token = substr($authorizationHeader, 7);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            return $this->json(['message' => 'Invalid token']);
        }

        if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
            return $this->json(['message' => 'No permission']);
        }

        
        $userProviders = $this->entityManager->getRepository(UserProvider::class)->findAll();

        return $this->json([
            'message' => 'User-Provider associations retrieved successfully',
            'result' => $userProviders
        ], 200, [], ['groups' => 'userprovider:read']);
        
    }

    //Supprimer 
    #[Route('/admin/user-provider/{userId}/{providerId}', name: 'delete_user_provider', methods: ['DELETE'])]
    public function deleteUserProvider(int $userId, int $providerId, Request $request): JsonResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            return $this->json(['message' => 'No token provided']);
        }

        $token = substr($authorizationHeader, 7);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            return $this->json(['message' => 'Invalid token']);
        }

        if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
            return $this->json(['message' => 'No permission']);
        }

        $userProvider = $this->entityManager->getRepository(UserProvider::class)->findOneBy([
            'user' => $userId,
            'provider' => $providerId
        ]);

        if (!$userProvider) {
            return $this->json(['message' => 'User-Provider relation not found']);
        }

        $this->entityManager->remove($userProvider);
        $this->entityManager->flush();

        return $this->json(['message' => 'User-Provider link deleted successfully']);
    }




}
