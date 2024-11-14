<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskUserCompletedController extends AbstractController
{
    #[Route('/task/user/completed', name: 'app_task_user_completed')]
    public function index(): Response
    {
        return $this->render('task_user_completed/index.html.twig', [
            'controller_name' => 'TaskUserCompletedController',
        ]);
    }
}
