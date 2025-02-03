<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Log;
use App\Entity\PostActu;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StatController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}


    #[Route('/stats/global', name: 'app_stats_global')]
    public function getStatsGlobal(Request $request): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $myUser = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$myUser) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $myUser->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }


            $users = $this->entityManager->getRepository(User::class)->findAll();
            $nb_user = count($users);

            $nb_owner = 0;
            $nb_admin = 0;
            $nb_modo_res = 0;
            $nb_modo_super = 0;
            $nb_modo = 0;
            $nb_write_res = 0;
            $nb_write_super = 0;
            $nb_write = 0;
            $nb_test_res = 0;
            $nb_test = 0;
            $nb_provider_admin = 0;
            $nb_provider = 0;
            $nb_beta = 0;
            $nb_ban = 0;

            foreach ($users as $user) {

                if (array_intersect(['ROLE_OWNER'], $user->getRoles())) {
                    $nb_owner++;
                }

                if (array_intersect(['ROLE_ADMIN'], $user->getRoles())) {
                    $nb_admin++;
                }

                if (array_intersect(['ROLE_MODO_RESPONSABLE'], $user->getRoles())) {
                    $nb_modo_res++;
                }

                if (array_intersect(['ROLE_MODO_SUPER'], $user->getRoles())) {
                    $nb_modo_super++;
                }

                if (array_intersect(['ROLE_MODO'], $user->getRoles())) {
                    $nb_modo++;
                }

                if (array_intersect(['ROLE_WRITE_RESPONSABLE'], $user->getRoles())) {
                    $nb_write_res++;
                }

                if (array_intersect(['ROLE_WRITE_SUPER'], $user->getRoles())) {
                    $nb_write_super++;
                }

                if (array_intersect(['ROLE_WRITE'], $user->getRoles())) {
                    $nb_write++;
                }

                if (array_intersect(['ROLE_TEST_RESPONSABLE'], $user->getRoles())) {
                    $nb_test_res++;
                }

                if (array_intersect(['ROLE_TEST'], $user->getRoles())) {
                    $nb_test++;
                }

                if (array_intersect(['ROLE_BAN'], $user->getRoles())) {
                    $nb_ban++;
                }

                if (array_intersect(['ROLE_BETA'], $user->getRoles())) {
                    $nb_beta++;
                }



            }

            $nb_game = $this->entityManager->getRepository(Game::class)->count();

            $nb_actu = $this->entityManager->getRepository(PostActu::class)->count();

            $result = [
                'nb_user' => $nb_user,
                'roles' => [
                    'nb_owner' => $nb_owner,
                    'nb_admin' => $nb_admin,
                    'nb_modo_red' => $nb_modo_res,
                    'nb_modo_super' => $nb_modo_super,
                    'nb_modo' => $nb_modo,
                    'nb_write_res' => $nb_write_res,
                    'nb_write_super' => $nb_write_super,
                    'nb_write' => $nb_write,
                    'nb_test_res' => $nb_test_res,
                    'nb_test' => $nb_test,
                    'nb_provider_admin' => $nb_provider_admin,
                    'nb_provider' => $nb_provider,
                    'nb_beta' => $nb_beta,
                    'nb_ban' => $nb_ban,
                ],
                'nb_game' => $nb_game,
                'nb_actu' => $nb_actu,
            ];

            return $this->json(['message' => 'good', 'result' => $result]);

        } else {

            return $this->json(['message' => 'no token']);
            
        }

    }

    #[Route('/stats/sanction', name: 'app_stats_sanction')]
    public function getStatsSanction(Request $request): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $myUser = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$myUser) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $myUser->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $nb_sanction = $this->entityManager->getRepository(Log::class)->count();

            $nb_pp_delete = $this->entityManager->getRepository(Log::class)->count(['why' => 'PP DELETE']);

            $result = [
                'nb_sanction' => $nb_sanction,
                'types' => [
                    'PP DELETE' => $nb_pp_delete,
                ],
            ];

            return $this->json(['message' => 'good', 'result' => $result]);

        } else {

            return $this->json(['message' => 'no token']);

        }

    }







}
