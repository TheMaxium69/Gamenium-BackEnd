<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogActuController extends AbstractController
{
    #[Route('/log/actu', name: 'app_log_actu')]
    public function index(): Response
    {
        return $this->render('log_actu/index.html.twig', [
            'controller_name' => 'LogActuController',
        ]);
    }
}
