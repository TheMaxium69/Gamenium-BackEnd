<?php

namespace App\Controller;

use App\Entity\HistoryMyGame;
use App\Entity\User;
use App\Entity\UserRate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UseritiumController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/useritium/{id}', name: 'app_useritium')]
    public function getProfil(int $id): JsonResponse
    {

        $user =  $this->entityManager->getRepository(User::class)->findOneBy(['id_useritium'=>$id]);

        if (!$user){
            return $this->json(['message' => 'user not found']);
        }


        $games = $this->entityManager->getRepository(HistoryMyGame::class)->findBy(['user' => $user]);
        $nb_game = count($games);

        $rates = $this->entityManager->getRepository(UserRate::class)->findBy(['user' => $user]);
        $nb_game_rate = count($rates);


        /* Photo de profil et couleur */
        /* Renvoyé le nombre de jeux */
        /* Renvoyé le nombre de jeux noté */

        $ppUrl = '';
        if ($user->getPp() !== null){
            $ppUrl = $user->getPp()->getUrl();
        }


        $result = [
            'id_useritium'=> $id,
            'username' => $user->getUsername(),
            'displayname' => $user->getDisplayname(),
            'displayname_useritium' => $user->getDisplaynameUseritium(),
            'pp'=> $ppUrl,
            'color'=> $user->getColor(),
            'nb_game'=> $nb_game,
            'nb_game_rate'=> $nb_game_rate,
        ];



        return $this->json(['message' => 'good', 'result' => $result], 200, [], ['groups' => 'user:read']);

    }
}
