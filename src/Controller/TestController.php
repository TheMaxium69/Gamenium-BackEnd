<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/getIp', name: 'app_test', methods: ['GET'])]
    public function getIp(Request $request): Response
    {

        $ip = $request->getClientIp();

        return $this->json([ "message" => "good", "result" => $ip], 200);
    }
}
