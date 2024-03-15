<?php

namespace App\Controller;

use App\Entity\SocialNetwork;
use App\Repository\SocialNetworkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SocialNetworkController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SocialNetworkRepository $socialNetworkRepository
    ) {}

        #[Route('/socialnetworks', name:'socialNetwork_all', methods:'GET')]

        public function getAllSocialNetworks(): JsonResponse
        {
            $socialNetworks = $this->socialNetworkRepository->findAll();

            if(!$socialNetworks){
                return $this->json(['message' => 'SocialNetwork not found']);
            }else {
                $message = [
                    'message' => "good",
                    'result' => $socialNetworks
                ];

                return $this->json($message , 200 , [], ['groups' => 'socialNetwork:read']);
        }}
}
