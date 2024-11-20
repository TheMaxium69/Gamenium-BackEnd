<?php

namespace App\Controller;

use App\Repository\HmgCopyRegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HmgCopyRegionController extends AbstractController
{
    public function __construct(
        private HmgCopyRegionRepository $hmgCopyRegionRepository
    ) {}


    #[Route('/hmgCopyRegionAll', name: 'app_hmgcopyregion')]
    public function getAllHmgCopyRegion(): Response
    {

        $copyRegions = $this->hmgCopyRegionRepository->findAll();

        return $this->json(['message' => 'good', 'result' => $copyRegions], 200, [], ['groups' => 'copyRegion:read']);
    }

    #[Route('/hmgCopyRegion/{id}', name: 'app_hmgcopyregion_one')]
    public function getOneHmgCopyRegion(int $id): Response
    {
        $copyRegion = $this->hmgCopyRegionRepository->find($id);

        if (!$copyRegion) {
            return $this->json(['message' => 'hmgCopyRegion not found']);
        }

        return $this->json(['message' => 'good', 'result' => $copyRegion], 200, [], ['groups' => 'copyRegion:read']);
    }
}
