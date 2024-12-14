<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test', methods: ['GET'])]
    public function index(Request $request): Response
    {

        $ip = $request->getClientIp();

        $ips = $request->getClientIps();




        return $this->json([
            "ip" => $ip,
            "ips" => $ips,
            "httpHost" => $request->getHttpHost(),
            "host" => $request->getHost(),
            "port" => $request->getPort(),
            "Languages" => $request->getLanguages(),
        ]);
    }
}
