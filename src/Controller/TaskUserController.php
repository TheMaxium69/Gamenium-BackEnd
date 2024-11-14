<?php

namespace App\Controller;

use App\Repository\TaskUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task-user')]
class TaskUserController extends AbstractController
{

    public function __construct(
        private TaskUserRepository $taskUserRepository
    ) {}

    #[Route('/view', name: 'app_task_user_view',  methods: ['GET'])]
    public function viewTaskUser(): Response
    {

        $taskUserAll = $this->taskUserRepository->findAll();

        if(!$taskUserAll){
            return $this->json(['message' => 'Task user not found']);
        } else {
            $message = [
                'message' => "good",
                'result' => $taskUserAll
            ];

            return $this->json($message , 200 , [], ['groups' => 'taskuser:read']);
        }

    }
}
