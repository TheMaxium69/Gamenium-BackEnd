<?php

namespace App\Controller;

use App\Entity\BuyWhere;
use App\Repository\BuyWhereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuyWhereController extends AbstractController
{
    private $manager;
    private $buywhere;

    public function __construct(EntityManagerInterface $manager, BuyWhereRepository $buywhere)
    {
        $this->manager = $manager;
        $this->buywhere = $buywhere;
    }

    #[Route('/buywhere/', name: 'all_places', methods:"GET")]

    public function getAllPlaces():JSONResponse
    {
        $buywhere = $this->buywhere->findAll();
        return $this->json($buywhere);
    }

    #[Route('/buywhere/{id}', name: 'place_by_id', methods:"GET")]

    public function getPlaceById(int $id): JSONResponse
    {
        $buywhere = $this->buywhere->find($id);
        return $this->json($buywhere);
    }

    #[Route('/buywhere', name: 'create_place', methods: ['POST'])]

    public function createPlace(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $buywhere = new Buywhere();
        $buywhere->setIsPublic($data['is_public']);
        $buywhere->setIdUser($data['id_user']);
        $buywhere->setName($data['name']);
        $buywhere->setCreatedAt(new \DateTime());
        $buywhere->setIp($data['ip']);

        $this->manager->persist($buywhere);
        $this->manager->flush();

        return $this->json(['message' => 'Buy Where created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/buywhere/{id}', name: 'place_delete', methods: ['DELETE'])]

    public function deleteBuyWhere(int $id):JsonResponse
    {
        $buywhere=$this->buywhere->find($id);

        if(!$buywhere) {
            return $this->json(['message' => 'Buys Where not found'], Response::HTTP_NOT_FOUND );
        }

        $this->manager->remove($buywhere);
        $this->manager->flush();

        return $this->json(['message' => 'Buy Where deleted successfully']);
    }
}

