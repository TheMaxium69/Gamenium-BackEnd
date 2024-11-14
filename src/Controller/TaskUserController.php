<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task-user')]
class TaskUserController extends AbstractController
{
    #[Route('/view', name: 'app_task_user_view')]
    public function viewTaskUser(): Response
    {




        return $this->json("");

    }
}
