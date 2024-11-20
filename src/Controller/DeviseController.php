<?php

namespace App\Controller;

use App\Repository\DeviseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeviseController extends AbstractController
{

    public function __construct(
        private DeviseRepository $deviseRepository
    ) {}


    #[Route('/devises', name: 'app_devise')]
    public function getAllDevise(): Response
    {

        $devises = $this->deviseRepository->findAll();

        return $this->json(['message' => 'good', 'result' => $devises], 200, [], ['groups' => 'devise:read']);
    }

    #[Route('/devise/{id}', name: 'app_devise_one')]
    public function getOneDevise(int $id): Response
    {
        $devise = $this->deviseRepository->find($id);

        if (!$devise) {
            return $this->json(['message' => 'Devise not found']);
        }

        return $this->json(['message' => 'good', 'result' => $devise], 200, [], ['groups' => 'devise:read']);
    }
}

