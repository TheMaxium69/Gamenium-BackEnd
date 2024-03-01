<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;   

class PictureController extends AbstractController
{

    private $manager;
    private $picture;

    public function __construct(EntityManagerInterface $manager, PictureRepository $picture)
    {
        $this->manager = $manager;
        $this->picture = $picture;
    }

    #[Route('/pictures', name: 'picture_all', methods:"GET")]
    public function getPictureAll():JsonResponse
    {
        $pictures = $this->picture->findAll();
        return $this->json($pictures, 200 , [], ['groups' => 'picture:read']);
    }

    #[Route('/picture/{id}', name: 'picture_by_id', methods:"GET")]
    public function getPictureById(int $id):JsonResponse
    {
        $picture = $this->picture->find($id);
        return $this->json($picture);
    }

    #[Route('/picture', name: 'create_picture', methods: ['POST'])]
    public function createPicture(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $picture = new Picture();
        $picture->setUrl($data['url']);
        $picture->setIdUser($data['id_user']);
        $picture->setPostedAt(new \DateTime());
        $picture->setIp($data['ip']);

        $this->manager->persist($picture);
        $this->manager->flush();

        return $this->json(['message' => 'Picture created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/picture/{id}', name: 'picture_delete', methods:"DELETE")]
    public function deletePicture(int $id):JsonResponse
    {
        $picture=$this->picture->find($id);

        if(!$picture) {
            return $this->json(['message' => 'Picture not found'], Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($picture);
        $this->manager->flush();

        return $this->json(['message' => 'Picture deleted successfully']);
    }
}

