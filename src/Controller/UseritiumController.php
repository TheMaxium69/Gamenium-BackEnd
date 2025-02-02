<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UseritiumController extends AbstractController
{
    #[Route('/useritium/{id}', name: 'app_useritium')]
    public function getProfil(int $id): JsonResponse
    {

        return $this->json(['message' => 'good', 'result' => $id], 200, [], ['groups' => 'user:read']);

    }
}
