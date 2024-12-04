<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('view')]
class ViewController extends AbstractController
{
    #[Route('-actu-add', name: 'app_view_actu_add', methods: ['POST'])]
    public function addActuView(): Response
    {
        return $this->json('');
    }
}
