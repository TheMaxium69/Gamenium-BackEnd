<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskUserController extends AbstractController
{
    #[Route('/task/user', name: 'app_task_user')]
    public function index(): Response
    {
        return $this->render('task_user/index.html.twig', [
            'controller_name' => 'TaskUserController',
        ]);
    }
}
