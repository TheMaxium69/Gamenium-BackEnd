<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\HistoryMyGame;
use App\Entity\HistoryMyPlateform;
use App\Entity\HmgCopy;
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
                    'ROLE_OWNER' => $nb_owner,
                    'ROLE_ADMIN' => $nb_admin,
                    'ROLE_MODO_RESPONSABLE' => $nb_modo_res,
                    'ROLE_MODO_SUPER' => $nb_modo_super,
                    'ROLE_MODO' => $nb_modo,
                    'ROLE_WRITE_RESPONSABLE' => $nb_write_res,
                    'ROLE_WRITE_SUPER' => $nb_write_super,
                    'ROLE_WRITE' => $nb_write,
                    'ROLE_TEST_RESPONSABLE' => $nb_test_res,
                    'ROLE_TEST' => $nb_test,
                    'ROLE_PROVIDER_ADMIN' => $nb_provider_admin,
                    'ROLE_PROVIDER' => $nb_provider,
                    'ROLE_BETA' => $nb_beta,
                    'ROLE_BAN' => $nb_ban,
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
            $nb_actu_delete = $this->entityManager->getRepository(Log::class)->count(['why' => 'ACTU DELETE']);
            $nb_hmg_edited = $this->entityManager->getRepository(Log::class)->count(['why' => 'HMG EDITED']);
            $nb_hmp_edited = $this->entityManager->getRepository(Log::class)->count(['why' => 'HMP EDITED']);
            $nb_comment_delete = $this->entityManager->getRepository(Log::class)->count(['why' => 'COMMENT DELETE']);
            $nb_screen_delete = $this->entityManager->getRepository(Log::class)->count(['why' => 'SCREEN DELETE']);
            $nb_user_ban = $this->entityManager->getRepository(Log::class)->count(['why' => 'BAN USER']);

            $result = [
                'nb_sanction' => $nb_sanction,
                'types' => [
                    'PP DELETE',
                    'ACTU DELETE',
                    'HMG EDITED',
                    'HMP EDITED',
                    'COMMENT DELETE',
                    'SCREEN DELETE',
                    'BAN USER'
                ],
                'nb_types' => [
                    $nb_pp_delete,
                    $nb_actu_delete,
                    $nb_hmg_edited,
                    $nb_hmp_edited,
                    $nb_comment_delete,
                    $nb_screen_delete,
                    $nb_user_ban
                ]
            ];

            return $this->json(['message' => 'good', 'result' => $result]);

        } else {

            return $this->json(['message' => 'no token']);

        }

    }


    #[Route('/stats/copy', name: 'app_stats_copy')]
    public function getStatsCopy(Request $request): JsonResponse
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

            $nb_hmg = $this->entityManager->getRepository(HistoryMyGame::class)->count();
            $nb_hmg_copy = $this->entityManager->getRepository(HmgCopy::class)->count();
            $hmg_average = $nb_hmg_copy / $nb_hmg;
            $nb_hmp = $this->entityManager->getRepository(HistoryMyPlateform::class)->count();
            $nb_hmp_copy = $this->entityManager->getRepository(HmgCopy::class)->count();
            $hmp_average = $nb_hmp_copy / $nb_hmp;

            $result = [
                'nb_hmg' => $nb_hmg,
                'nb_hmg_copy' => $nb_hmg_copy,
                'nb_hmg_average' => $hmg_average,
                'nb_hmp' => $nb_hmp,
                'nb_hmp_copy' => $nb_hmp_copy,
                'nb_hmp_average' => $hmp_average,
            ];

            return $this->json(['message' => 'good', 'result' => $result]);

        } else {

            return $this->json(['message' => 'no token']);

        }

    }

    #[Route('/stats/gameOne/{id}', name: 'app_stats_game_one')]
    public function getStatsGameOne(int $id, Request $request): JsonResponse
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

            $game = $this->entityManager->getRepository(Game::class)->findOneBy(['id' => $id]);
            if (!$game) {
                return $this->json(['message' => 'game not found']);
            }

            $hmgs = $this->entityManager->getRepository(HistoryMyGame::class)->findBy(['game' => $game]);

            $nb_hmg_copy = 0;
            foreach ($hmgs as $hmg) {
                $nb_hmg_copy = $nb_hmg_copy + $this->entityManager->getRepository(HmgCopy::class)->count(['HistoryMyGame' => $hmg]);
            }

            $result = [
                'hmg' => count($hmgs),
                'hmgCopy' => $nb_hmg_copy,
            ];

            return $this->json(['message' => 'good', 'result' => $result]);

        } else {

            return $this->json(['message' => 'no token']);

        }

    }








}
