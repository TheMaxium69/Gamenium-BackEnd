<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\LogActu;
use App\Entity\Picture;
use App\Entity\PostActu;
use App\Entity\Provider;
use App\Entity\User;
use App\Repository\PostActuRepository;
use App\Repository\ProviderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostActuController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PostActuRepository $postActuRepository,
        private ProviderRepository  $providerRepository

    ) {}

    #[Route('/postactus', name: 'get_all_postactus', methods: ['GET'])]
    public function getAllPostActus(): JsonResponse
    {
        $postActus = $this->postActuRepository->findAllOrderedByDate();

        if(!$postActus){
            return $this->json(['message' => 'PostActu not found']);
        } else {

            $postByProviderFinal = [];
            foreach ($postActus as $post) {

                if (!$post->isIsDeleted()) {
                    $postByProviderFinal[] = $post;
                }

            }

            $message = [
                'message' => "good",
                'result' => $postByProviderFinal
            ];

            return $this->json($message , 200 , [], ['groups' => 'post:read']);
        }

    }

    #[Route('/postactu/{id}', name: 'get_postactu_by_id', methods: ['GET'])]
    public function getPostActuById(int $id): JsonResponse
    {
        $postActu = $this->postActuRepository->find($id);

        if(!$postActu || $postActu->isIsDeleted()){
            return $this->json(['message' => 'Post Actu not found']);
        } else {
            $message = [
                'message' => "good",
                'result' => $postActu
            ];

            return $this->json($message, 200 , [], ['groups' => 'post:read']);
        }
    }

   #[Route('/postactus', name: 'create_postactu', methods: ['POST'])]
   public function createPostActu(Request $request): JsonResponse
   {
       $data = json_decode($request->getContent(), true);

       //verification JSON validité
       if($data === null && json_last_error() !==JSON_ERROR_NONE) {
        return $this->json(['message' => "invalid JSON format"]);
       }

       //validation des données requies
       if (!isset($data['title'], $data['content'], $data['picture_id'], $data['provider_id'])) {
        return $this->json(['message' => 'Missing required fields']);
       }

       $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

        /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
        if (!$user){
            return $this->json(['message' => 'token is failed']);
        }

        if (!in_array('ROLE_OWNER', $user->getRoles()) && !in_array('ROLE_ADMIN', $user->getRoles()) && !in_array('ROLE_WRITE_RESPONSABLE', $user->getRoles()) && !in_array('ROLE_WRITE_SUPER', $user->getRoles()) && !in_array('ROLE_WRITE', $user->getRoles())) {
            return $this->json(['message' => 'no permission']);
        }

        // Recherche de jeu
        $game = $this->entityManager->getRepository(Game::class)->find($data['game_id']);

        // Recherche de photo
        $picture = $this->entityManager->getRepository(Picture::class)->find($data['picture_id']);
        if (!$picture) {
            return $this->json(['message' => 'unknown picture']);
        }

        //Si Provider fournis
        $provider = $this->entityManager->getRepository(Provider::class)->find($data['provider_id']);
        if (!$provider) {
            return $this->json(['message' => 'Unknown provider']);
        }
        
        $postActu = new PostActu();
        $postActu->setTitle($data['title']);
        $postActu->setContent($data['content']);
        $postActu->setCreatedAt(new \DateTimeImmutable($data['created_at']));
        // $postActu->setLastEdit($data['last_edit']);
        $postActu->setNbEdit($data['nb_edit'] ?? 0);
        $postActu->setUser($user);
        $postActu->setGame($game);
        $postActu->setPicture($picture);
        $postActu->setIsDeleted(false);
        if ($provider !== null) {
            $postActu->setProvider($provider);
        }
        
        $this->entityManager->persist($postActu);

         /* LOG */
        $logActu = new LogActu();
        $logActu->setUser($user);  
        $logActu->setActu($postActu);  
        $logActu->setAction("CREATE");
        $logActu->setRoute("WRITE");
        $logActu->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($logActu);

        $this->entityManager->flush();

    
    }

       return $this->json(['message' => 'PostActu created successfully'], Response::HTTP_CREATED);
   }

    #[Route('/NbPostByProvider/{id}', name: 'get_postactus_by_provider', methods: ['GET'])]
    public function getPostActuByProvider(int $id): JsonResponse
    {

        $provider = $this->providerRepository->find($id);

    
        if (!$provider) {
            return $this->json(['error' => 'Provider non trouvé']);
        }

        $postCount = $this->postActuRepository->count(['Provider' => $id]);

        $response = [
            'message' => 'good',
            'result' => $postCount,
        ];

        return $this->json($response);
    }

    #[Route('/postByProvider/{id}', name: 'get_postactus_by_provider', methods: ['GET'])]
    public function getPostByProvider(int $id): JsonResponse
    {
        $provider = $this->providerRepository->find($id);

        if (!$provider) {
            return $this->json(['error' => 'Provider non trouvé']);
        }

        $postByProvider = $this->postActuRepository->findByProviderOrderedByDate($provider);


        if ($postByProvider) {

            $postByProviderFinal = [];
            foreach ($postByProvider as $post) {

                if (!$post->isIsDeleted()) {
                    $postByProviderFinal[] = $post;
                }

            }

            $response = [
                'message' => 'good',
                'result' => $postByProviderFinal,
            ];


        } else {
            $response = [
                'message' => 'good',
                'result' => $postByProvider,
            ];

        }
        


        return $this->json($response, 200, [], ['groups' => 'post:read']);
    }



    #[Route('/postactus/search', name: 'search_postactus', methods: ['POST'])]
    public function searchPostActu(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $searchValue = $data['searchValue'] ?? '';
        $limit = $data['limit'];

        $results = $this->postActuRepository->searchPostActuByName($searchValue, $limit);

        $postByProviderFinal = [];
        foreach ($results as $post) {

            if (!$post->isIsDeleted()) {
                $postByProviderFinal[] = $post;
            }

        }


        return $this->json($postByProviderFinal, 200, [], ['groups' => 'post:read']);
    }



    #[Route('/latestactubyprovider/{id}', name: 'get_latest_actu_by_provider', methods: ['GET'])]
    public function getLatestActuByProvider(int $id): JsonResponse
    {
        $provider = $this->providerRepository->find($id);

        if (!$provider) {
            return $this->json(['error' => 'Provider non trouvé']);
        }

        $latestActus = $this->postActuRepository->findLatestByProvider($provider);

        if (empty($latestActus)) {
            return $this->json(['message' => 'Le fournisseur n\'a pas d\'actualités']);
        }

        $postByProviderFinal = [];
        foreach ($latestActus as $post) {

            if (!$post->isIsDeleted()) {
                $postByProviderFinal[] = $post;
            }

        }

        $response = [
            'message' => 'good',
            'result' => $postByProviderFinal,
        ];

        return $this->json($response, 200, [], ['groups' => 'post:read']);
    }




    #[Route('/postactus/{id}', name: 'update_postactu', methods: ['PUT'])]
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

        $userRoles = $user->getRoles();
        $isOwner = $postActu->getUser()->getId() === $user->getId();
        $canModifyAll = in_array('ROLE_WRITE_SUPER', $userRoles) || in_array('ROLE_WRITE_RESPONSABLE', $userRoles) || in_array('ROLE_PROVIDER', $userRoles) || in_array('ROLE_PROVIDER_ADMIN', $userRoles);

        if (!$isOwner && !$canModifyAll) {
            return $this->json(['message' => 'You do not have permission to edit this post'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        // Vérif si changement on été fait
        $hasChanges = false;

        if (isset($data['title']) && $data['title'] !== $postActu->getTitle()) {
            $postActu->setTitle($data['title']);
            $hasChanges = true;
        }
        if (isset($data['content']) && $data['content'] !== $postActu->getContent()) {
            $postActu->setContent($data['content']);
            $hasChanges = true;
        }
        if (isset($data['game_id'])) {
            $game = $this->entityManager->getRepository(Game::class)->find($data['game_id']);
            if ($game && $game !== $postActu->getGame()) {
                $postActu->setGame($game);
                $hasChanges = true;
            }
        }
        if (isset($data['provider_id'])) {
            $provider = $this->entityManager->getRepository(Provider::class)->find($data['provider_id']);
            if ($provider && $provider !== $postActu->getProvider()) {
                $postActu->setProvider($provider);
                $hasChanges = true;
            }
        }
        if (isset($data['picture_id'])) {
            $picture = $this->entityManager->getRepository(Picture::class)->find($data['picture_id']);
            if ($picture) {
                $postActu->setPicture($picture);
            } else {
                return $this->json(['message' => 'Invalid picture ID'], Response::HTTP_BAD_REQUEST);
            }
        }

        // Si pas de changement on log pas 
        if (!$hasChanges) {
            return $this->json(['message' => 'No changes detected']);
        }

        $postActu->setLastEdit(new \DateTime());
        $postActu->setNbEdit(($postActu->getNbEdit() ?? 0) + 1);

        $this->entityManager->persist($postActu);

        /* LOG */
        $logActu = new LogActu();
        $logActu->setUser($user);
        $logActu->setActu($postActu); 
        $logActu->setAction("EDIT");
        $logActu->setRoute("WRITE");
        $logActu->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($logActu);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'PostActu updated successfully'
        ], Response::HTTP_OK, [], ['groups' => 'post:read']);
    }

    #[Route('/postactus/delete/{id}', name: 'delete_postactu', methods: ['PUT'])]
    public function deletePostActuIsDelete(int $id, Request $request): JsonResponse
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

        $userRoles = $user->getRoles();
        $isOwner = $postActu->getUser()->getId() === $user->getId();
        $canModifyAll = in_array('ROLE_WRITE_RESPONSABLE', $userRoles) || in_array('ROLE_ADMIN', $userRoles) || in_array('ROLE_OWNER', $userRoles) || in_array('ROLE_MODO_RESPONSABLE', $userRoles) || in_array('ROLE_MODO_SUPER', $userRoles) || in_array('ROLE_MODO', $userRoles);

        if (!$isOwner && !$canModifyAll) {
            return $this->json(['message' => 'You do not have permission to delete this post']);
        }

        $postActu->setIsDeleted(true);
        $this->entityManager->persist($postActu);

        $logActu = new LogActu();
        $logActu->setUser($user);
        $logActu->setActu($postActu);
        $logActu->setAction("DELETE");
        $logActu->setRoute("WRITE");
        $logActu->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($logActu);
        $this->entityManager->flush();

        return $this->json(['message' => 'PostActu marked as deleted successfully']);
    }

}