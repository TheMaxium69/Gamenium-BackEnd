<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Entity\BadgeVersUser;
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

    #[Route('/badges', name: 'get_all_badges', methods: ['GET'])]
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

    #[Route('/addbadge', name: 'add_badge', methods: ['POST'])]
    public function addBadge(Request $request): JsonResponse
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

            $badgeToUser = new BadgeVersUser();
            $badgeToUser->setUser($pendingUser);
            $badgeToUser->setBadge($badge);
            $badgeToUser->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($badgeToUser);
            $this->entityManager->flush();

            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }


    }


}
