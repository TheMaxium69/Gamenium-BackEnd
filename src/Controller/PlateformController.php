<?php

namespace App\Controller;

use App\Entity\HistoryMyGame;
use App\Entity\User;
use App\Repository\PlateformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlateformController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PlateformRepository $plateformRepository
    ) {}

    #[Route('/plateforms', name: 'get_all_plateforms', methods: ['GET'])]
    public function getPlateformAll(): JsonResponse
    {
        $plateforms = $this->plateformRepository->findAll();

        if(!$plateforms){
            return $this->json(['message' => 'Plateforms not found']);
        } else {
            $message = [
                'message' => "good",
                'result' => $plateforms
            ];
        }

        return $this->json($message, 200 , [], ['groups' => 'plateform:read']);
    }

    #[Route('/plateform/{id}', name: 'get_one_plateform')]
    public function getPlateformOne(int $id): Response
    {
        $plateform = $this->plateformRepository->find($id);

        if (!$plateform) {
            return $this->json(['message' => 'Plateform not found']);
        }

        return $this->json(['message' => 'good', 'result' => $plateform], 200, [], ['groups' => 'plateform:read']);
    }

    #[Route('/plateformByIGB/{id}', name: 'get_one_plateform_igb')]
    public function getPlateformOneByIGB(int $id): Response
    {
        $plateform = $this->plateformRepository->findBy(['id_giant_bomb' => $id]);

        if (!$plateform) {
            return $this->json(['message' => 'Plateform not found']);
        }

        return $this->json(['message' => 'good', 'result' => $plateform], 200, [], ['groups' => 'plateform:read']);
    }

    #[Route('/plateformWithUser/{id}', name: 'get_plateform_user')]
    public function getPlateformWithUser(int $id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        $myGameAll = $this->entityManager->getRepository(HistoryMyGame::class)->findBy(['user' => $user]);

        if (!$myGameAll) {
            return $this->json(['message' => 'no game']);
        }

        $allPlateformes = [];
        foreach ($myGameAll as $game) {
            $plateforme = $game->getPlateform();
            $allPlateformes[$game->getPlateform()->getId()] = $plateforme;
        }

        $plateformesUniques = array_values($allPlateformes);

        return $this->json(['message' => 'good', 'result' => $plateformesUniques], 200, [], ['groups' => 'plateform:read']);
    }
}
