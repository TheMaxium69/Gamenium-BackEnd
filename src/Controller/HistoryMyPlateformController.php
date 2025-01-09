<?php

namespace App\Controller;

use App\Entity\HmpCopy;
use App\Entity\Plateform;
use App\Entity\User;
use App\Repository\HistoryMyPlateformRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryMyPlateformController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private HistoryMyPlateformRepository $historyMyPlateformRepository
    ){}
    #[Route('/OneMyPlateform/{id_user}/{id_plateform}', name: 'get_one_hmp_by_user', methods: ['GET'])]
    public function getOneMyHmpByUser(int $id_user, int $id_plateform): JsonResponse
    {

        $plateform = $this->entityManager->getRepository(Plateform::class)->find($id_plateform);
        if (!$plateform){
            return $this->json(['message' => 'plateform not found']);
        }

        $user = $this->entityManager->getRepository(User::class)->find($id_user);
        if (!$user){
            return $this->json(['message' => 'user not found']);
        }


        $myPlateform = $this->historyMyPlateformRepository->findOneBy(['user' => $user, 'plateform' => $plateform]);

        $copyPlateform = $this->entityManager->getRepository(HmpCopy::class)->findBy(['history_my_plateform' => $myPlateform]);
        var_dump($copyPlateform);

        $message = [
            'message' => "good",
            'result' => [
                "id" => $myPlateform->getId(),
                "myPlateform" => $myPlateform,
//                "copyPlateform" => $copyPlateform
            ]
            ];

        return $this->json($message, 200, [], ['groups' => 'historyplateform:read']);
    }


    #[Route('/addHmp', name: 'addHmp', methods: ['POST'])]
    public function addHmp(Request $request): JsonResponse
    {

    }

    #[Route('/deleteHmp/{id}', name: 'deleteHmp', methods: ['DELETE'])]
    public function deleteHmp(Request $request, int $id): JsonResponse
    {

    } 
}
