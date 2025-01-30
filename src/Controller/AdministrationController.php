<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin')]
class AdministrationController extends AbstractController
{
    #[Route('-test', name: 'app_administration')]
    public function test(): JsonResponse
    {



        return $this->json(['message' => 'good']);

    }
}
