<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Entity\BadgeVersUser;
use App\Entity\Picture;
use App\Entity\User;
use App\Repository\BadgeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BadgeController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BadgeRepository $badgeRepository
    ) {}

    #[Route('/badge-all', name: 'get_all_badges', methods: ['GET'])]
    public function getAllBadges(): JsonResponse
    {
        $badges = $this->badgeRepository->findAll();

        return $this->json(['result' => $badges, 'message' => 'good'], 200, [], ['groups' => 'badge:read']);
    }

    #[Route('/badge/{id}', name: 'get_badge_by_id', methods: ['GET'])]
    public function getBadgeById(int $id): JsonResponse
    {
        $badge = $this->badgeRepository->find($id);

        if (!$badge) {
            return $this->json(['message' => 'Badge not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($badge);
    }

    #[Route('/badges/user/{id}', name: 'get_badges_by_user', methods: ['GET'])]
    public function getBadgesByUser(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);



        if (!$user){

            return $this->json(['message' => 'user not found'], 200, [], ['groups' => 'badge:read']);

        } else {

            $badgeToUserRepository = $this->entityManager->getRepository(BadgeVersUser::class);
            $badgeToUserEntries = $badgeToUserRepository->findBy(['user' => $user]);

            $badges = [];
            foreach ($badgeToUserEntries as $entry) {
                $badges[] = $entry->getBadge();
            }

            if ($badges == []){

                $message = [
                    'message' => "nada"
                ];

            } else {

                $message = [
                    'message' => "good",
                    'result' => $badges
                ];

            }

            return $this->json($message, 200, [], ['groups' => 'badge:read']);
        }
    }

    #[Route('/togglebadge', name: 'toggle_badge', methods: ['POST'])]
    public function toggleBadge(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id_user']) && !isset($data['id_badge'])){
            return $this->json(['message' => 'undefine of field']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $badge = $this->entityManager->getRepository(Badge::class)->findOneBy(['id' => $data['id_badge']]);
            if (!$badge) {
                return $this->json(['message' => 'badge not found']);
            }

            $pendingUser = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['id_user']]);
            if (!$pendingUser) {
                return $this->json(['message' => 'user not found']);
            }


            // Check if the pendingUser already has this badge
            $existingBadgeToUser = $this->entityManager->getRepository(BadgeVersUser::class)->findOneBy(['user' => $pendingUser, 'badge' => $badge]);
            if ($existingBadgeToUser) {

                $this->entityManager->remove($existingBadgeToUser);
                $this->entityManager->flush();

                return $this->json(['message' => 'delete success']);

            } else {
                $badgeToUser = new BadgeVersUser();
                $badgeToUser->setUser($pendingUser);
                $badgeToUser->setBadge($badge);
                $badgeToUser->setCreatedAt(new \DateTimeImmutable());

                $this->entityManager->persist($badgeToUser);
                $this->entityManager->flush();

                return $this->json(['message' => 'add success']);
            }




        } else {
            return $this->json(['message' => 'no token']);
        }


    }

    #[Route('/remove-badge/{id}', name: 'remove_badge', methods: ['DELETE'])]
    public function removeBadge(int $id, Request $request): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $badge = $this->entityManager->getRepository(Badge::class)->findOneBy(['id' => $id]);
            if (!$badge) {
                return $this->json(['message' => 'badge not found']);
            }

            // Check if les gens on le badge
            $existingBadgeToUser = $this->entityManager->getRepository(BadgeVersUser::class)->findBy(['badge' => $badge]);
            if ($existingBadgeToUser) {
                foreach ($existingBadgeToUser as $oneBadgeUser) {
                   $this->entityManager->remove($oneBadgeUser);
                   $this->entityManager->flush();
                }
            }

            $this->entityManager->remove($badge);
            $this->entityManager->flush();
            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }


    }

    #[Route('/user-with-badge/{id}', name: 'user_with_badge', methods: ['GET'])]
    public function getUserWithBadge(int $id, Request $request): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'no permission']);
            }

            if (!in_array('ROLE_OWNER', $user->getRoles()) &&
                !in_array('ROLE_ADMIN', $user->getRoles()) &&
                !in_array('ROLE_MODO_RESPONSABLE', $user->getRoles()) &&
                !in_array('ROLE_MODO_SUPER', $user->getRoles()) &&
                !in_array('ROLE_MODO', $user->getRoles()) &&
                !in_array('ROLE_WRITE_RESPONSABLE', $user->getRoles()) &&
                !in_array('ROLE_WRITE_SUPER', $user->getRoles()) &&
                !in_array('ROLE_WRITE', $user->getRoles()) &&
                !in_array('ROLE_TEST_RESPONSABLE', $user->getRoles()) &&
                !in_array('ROLE_TEST', $user->getRoles()) &&
                !in_array('ROLE_PROVIDER_ADMIN', $user->getRoles()) &&
                !in_array('ROLE_PROVIDER', $user->getRoles()) ) {
                return $this->json(['message' => 'no permission']);
            }

            $badge = $this->entityManager->getRepository(Badge::class)->findOneBy(['id' => $id]);
            if (!$badge) {
                return $this->json(['message' => 'badge not found']);
            }

            $badgeVersUser = $this->entityManager->getRepository(BadgeVersUser::class)->findBy(['badge' => $badge]);

            $users = [];
            foreach ($badgeVersUser as $entry) {
                $users[] = $entry->getUser();
            }

            $result = [
                "message" => "good",
                "result" => $badge,
                "result2" => $users,
            ];

            return $this->json($result, 200, [], ['groups' => 'badge:read']);
        } else {
            return $this->json(['message' => 'no token']);
        }


    }

    #[Route('/create-badge', name: 'create_badge', methods: ['POST'])]
    public function createBadge(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        
        if (!isset($data['name'], $data['picture_id'], $data['unlockDescription'])) {
            return $this->json(['message' => 'Missing required fields']);
        }

        
        $authorizationHeader = $request->headers->get('Authorization');
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            return $this->json(['message' => 'No token provided']);
        }

        $token = substr($authorizationHeader, 7);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            return $this->json(['message' => 'Invalid token']);
        }

        if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
            return $this->json(['message' => 'No permission']);
        }

        
        $picture = $this->entityManager->getRepository(Picture::class)->find($data['picture_id']);
        if (!$picture) {
            return $this->json(['message' => 'Invalid picture ID']);
        }

        
        $badge = new Badge();
        $badge->setName($data['name']);
        $badge->setPicture($picture);
        $badge->setUnlockDescription($data['unlockDescription']);
        $badge->setCreatedAt(new \DateTimeImmutable());

        
        $this->entityManager->persist($badge);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Badge created successfully',
            'result' => $badge
        ], Response::HTTP_CREATED, [], ['groups' => 'badge:read']);
    }



}
