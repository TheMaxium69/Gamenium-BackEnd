<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/profil/{id}', name: 'get_profil_user_id')]
    public function getProfilByUser(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user){

            return $this->json(['message' => 'user not found']);

        } else {

            if ($user->getId() == 2){
                $reseau = [
                    [
                        "url" => "https://instagram.com/the_maxime_san",
                        "serice" => [
                            "id"=>0,
                            "name"=>"instagram",
                            "icon_class"=>"ri-instagram-line"
                        ]
                    ],
                    [
                        "url" => "https://github.com/TheMaxium69",
                        "serice" => [
                            "id"=>1,
                            "name"=>"github",
                            "icon_class"=>"ri-github-fill"
                        ]
                    ],
                    [
                        "url" => "https://linkedin.com/in/maxime-tournier-tyrolium",
                        "serice" => [
                            "id"=>2,
                            "name"=>"linkedin",
                            "icon_class"=>"ri-linkedin-box-fill"
                        ]
                    ],
                ];
            } else {
                $reseau = null;
            }

            $message = [
                'message' => "good",
                'result' => [
                    "id" => $user->getId(),
                    "username" => $user->getUsername(),
                    "displayname" => $user->getDisplayname(),
                    "displaynameUseritium" => $user->getDisplaynameUseritium(),
                    "joinAt" => $user->getJoinAt(),
                    "themeColor" => $user->getColor(),
                    "picture" => $user->getPp()->getUrl(),
                    "nbGame" => 10,
                    "nbNote" => 5,
                    "reseau" => $reseau
                ]
            ];

            return $this->json($message);
        }
    }







}
