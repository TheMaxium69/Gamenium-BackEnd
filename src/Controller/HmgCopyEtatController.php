<?php

namespace App\Controller;

use App\Repository\HmgCopyEtatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HmgCopyEtatController extends AbstractController
{

    public function __construct(
        private HmgCopyEtatRepository $hmgCopyEtatRepository
    ) {}


    #[Route('/hmgCopyEtatAll', name: 'app_hmgcopyetat')]
    public function getAllHmgCopyEtat(): Response
    {

        $copyEtats = $this->hmgCopyEtatRepository->findAll();

        return $this->json(['message' => 'good', 'result' => $copyEtats], 200, [], ['groups' => 'copyEtat:read']);
    }

    #[Route('/hmgCopyEtat/{id}', name: 'app_hmgcopyetat_one')]
    public function getOneHmgCopyEtat(int $id): Response
    {
        $copyEtat = $this->hmgCopyEtatRepository->find($id);

        if (!$copyEtat) {
            return $this->json(['message' => 'hmgCopyEtat not found']);
        }

        return $this->json(['message' => 'good', 'result' => $copyEtat], 200, [], ['groups' => 'copyEtat:read']);
    }
}
