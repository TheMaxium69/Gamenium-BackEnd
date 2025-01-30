<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Picture;
use App\Entity\PostActu;
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
            $message = [
                'message' => "good",
                'result' => $postActus
            ];

            return $this->json($message , 200 , [], ['groups' => 'post:read']);
        }

    }

    #[Route('/postactu/{id}', name: 'get_postactu_by_id', methods: ['GET'])]
    public function getPostActuById(int $id): JsonResponse
    {
        $postActu = $this->postActuRepository->find($id);

        if(!$postActu){
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
       if (!isset($data['title'], $data['content'], $data['picture'], $data['game_id'])) {
        return $this->json(['message' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
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

        // Recherche de jeu
        $game = $this->entityManager->getRepository(Game::class)->find($data['game_id']);
        if (!$game) {
            return $this->json(['message' => 'unknown game']);
        }

        // Recherche de photo
        $picture = $this->entityManager->getRepository(Picture::class)->find($data['picture_id']);
        if (!$picture) {
            return $this->json(['message' => 'unknown picture']);
        }

        
        $postActu = new PostActu();
        $postActu->setTitle($data['title']);
        $postActu->setContent($data['content']);
        $postActu->setCreatedAt(new \DateTimeImmutable($data['created_at']));
        $postActu->setLastEdit($data['last_edit']);
        $postActu->setNbEdit($data['nb_edit']);
        $postActu->setUser($user);
        $postActu->setGame($game);
        $postActu->setPicture($picture);
        
        $this->entityManager->persist($postActu);
        $this->entityManager->flush();
    }

       return $this->json(['message' => 'PostActu created successfully'], Response::HTTP_CREATED);
   }

   #[Route('/postactus/{id}', name: 'delete_postactu', methods: ['DELETE'])]
   public function deletePostActu(int $id): JsonResponse
   {
       $postActu = $this->postActuRepository->find($id);

       if (!$postActu) {
           return $this->json(['message' => 'PostActu not found'], Response::HTTP_NOT_FOUND);
       }

       $this->entityManager->remove($postActu);
       $this->entityManager->flush();

       return $this->json(['message' => 'PostActu deleted successfully']);
   }
    


    #[Route('/NbPostByProvider/{id}', name: 'get_postactus_by_provider', methods: ['GET'])]
    public function getPostActuByProvider(int $id): JsonResponse
    {

        $provider = $this->providerRepository->find($id);

        $postCount = $this->postActuRepository->count(['Provider' => $id]);
    
        if (!$provider) {
            return $this->json(['error' => 'Provider non trouvé'], Response::HTTP_NOT_FOUND);
        }

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
            return $this->json(['error' => 'Provider non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $postByProvider = $this->postActuRepository->findByProviderOrderedByDate($provider);

        $response = [
            'message' => 'good',
            'result' => $postByProvider,
        ];

        return $this->json($response, 200, [], ['groups' => 'post:read']);
    }



    #[Route('/postactus/search', name: 'search_postactus', methods: ['POST'])]
    public function searchPostActu(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $searchValue = $data['searchValue'] ?? '';
        $limit = $data['limit'];

        $results = $this->postActuRepository->searchPostActuByName($searchValue, $limit);
    
        return $this->json($results, 200, [], ['groups' => 'post:read']);
    }



    #[Route('/latestactubyprovider/{id}', name: 'get_latest_actu_by_provider', methods: ['GET'])]
    public function getLatestActuByProvider(int $id): JsonResponse
    {
    $provider = $this->providerRepository->find($id);

    if (!$provider) {
        return $this->json(['error' => 'Provider non trouvé'], Response::HTTP_NOT_FOUND);
    }

    $latestActus = $this->postActuRepository->findLatestByProvider($provider);

    if (empty($latestActus)) {
        return $this->json(['message' => 'Le fournisseur n\'a pas d\'actualités'], Response::HTTP_NOT_FOUND);
    }

    
    $response = [
        'message' => 'good',
        'result' => $latestActus,
    ];
    
    return $this->json($response, 200, [], ['groups' => 'post:read']);
    }
}