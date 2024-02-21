<?php

namespace App\Controller;

use App\Entity\GameActuality;
use App\Repository\GameActualityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameActualityController extends AbstractController
{
    public function __construct(
         private EntityManagerInterface $entityManager,
         private GameActualityRepository $gameActualityRepository) {}

    #[Route('/actualities', name: 'game_actuality_all', methods:"GET")]
    public function getGameActualityAll(): JsonResponse
    {
        $gameActualies = $this->gameActualityRepository->findAll();
        return $this->json($gameActualies, 200, [], ['groups' => 'game:read']);
        
    }

    #[Route('/actuality/{id}', name: 'game_actuality_by_id', methods:"GET")]
    public function getGameActualityById(int $id):JsonResponse
    {
        $gameActuality = $this->gameActualityRepository->find($id);

        if(!$gameActuality){
            return $this->json(['message' => 'actuality not found', Response::HTTP_NOT_FOUND]);
        }

        return $this->json($gameActuality);
    }

    #[Route('/actuality', name:'game_actuality_create', methods:"POST")]
    public function createGameActuality(Request $request): JsonResponse
    {
        $data = json_encode($request->getContent(), true);

        $idPicture = $data['idPicture'] ?? null;

        $gameActivity = new GameActuality();

        $gameActivity->setPictureId($idPicture);
        $gameActivity->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($gameActivity);
        $this->entityManager->flush();

        return $this->json(['message' => 'Game Actuality create succefuly', 'gameActuality' => $gameActivity]); 
    }

    #[Route('/actuality/{id}', name:"game_actuality_delete", methods:'DELETE')]
    public function deleteGameActuality(int $id): JsonResponse
    {
        $gameActuality = $this->gameActualityRepository->find($id);

        if(!$gameActuality){

            return $this-> json(['message' => 'Game actuality dnot Found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($gameActuality);
        $this->entityManager->flush();

        return $this->json(['message', 'Game deleted succefully']);
    }
}