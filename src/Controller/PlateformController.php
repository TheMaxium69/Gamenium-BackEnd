<?php

namespace App\Controller;

use App\Repository\PlateformRepository;
use App\Repository\PlatformRepository;
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
}
