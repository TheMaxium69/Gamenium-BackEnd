<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HmgTagsController extends AbstractController
{
    #[Route('/hmg/tags', name: 'app_hmg_tags')]
    public function index(): JsonResponse
    {
        return $this->json([]);
    }
}
