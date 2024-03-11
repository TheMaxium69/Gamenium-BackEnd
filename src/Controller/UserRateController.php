<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserRate;
use App\Repository\UserRateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserRateController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRateRepository $userRateRepository
    ) {}

    #[Route('/RatingByUser/{id}', name: 'get_rates_by_user', methods: ['GET'])]
    public function getRatesByUser(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user){

            return $this->json(['message' => 'user not found']);

        } else {

            $RatesToUserEntries = $this->userRateRepository->findBy(['user' => $user]);

            $userRates = [];
            foreach ($RatesToUserEntries as $entry) {
                $userRates[] = $entry;
            }

            if ($userRates == []){

                $message = [
                    'message' => "aucune note"
                ];

            } else {

                $message = [
                    'message' => "good",
                    'result' => $userRates
                ];

            }

            return $this->json($message, 200, [], ['groups' => 'userRate:read']);
        }
    }


    #[Route('/userRate/{id}', name: 'get_userRate_by_id', methods: ['GET'])]
    public function getuserRateById(int $id): JsonResponse
    {
        $userRate = $this->userRateRepository->find($id);

        if (!$userRate) {
            return $this->json(['message' => 'userRate not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($userRate);
    }

    #[Route('/userRate', name: 'create_userRate', methods: ['POST'])]
    public function createuserRate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $userRate = new UserRate();
        $userRate->setIdUser($data['idUser']);
        $userRate->setIdGame($data['idGame']);
        $userRate->setRating($data['rating']);
        $userRate->setCreatedAt(new \DateTimeImmutable);
        $userRate->setIp($data['ip']);

        $this->entityManager->persist($userRate);
        $this->entityManager->flush();

        return $this->json(['message' => 'userRate created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/userRate/{id}', name: 'delete_userRate', methods: ['DELETE'])]
    public function deleteuserRate(int $id): JsonResponse
    {
        $userRate = $this->userRateRepository->find($id);

        if (!$userRate) {
            return $this->json(['message' => 'userRate not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($userRate);
        $this->entityManager->flush();

        return $this->json(['message' => 'userRate deleted successfully']);
    }
}
