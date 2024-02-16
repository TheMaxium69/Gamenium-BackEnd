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

    #[Route('/pictures/', name: 'picture_all', methods:"GET")]
    public function getPictureAll():JsonResponse
    {
        $pictures = $this->picture->findAll();
        return $this->json($pictures);
    }

    #[Route('/picture/{id}', name: 'picture_by_id', methods:"GET")]
    public function getPictureById(Picture $picture):JsonResponse
    {
        return $this->json(['id' => $picture->getId()]);
    }

    #[Route('/picture/', name: 'picture_create', methods:"POST")]
    public function createPicture (Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $picture = new Picture();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($picture);
        $entityManager->flush();

        return $this->json(['message' => 'Picture created successfully', 'id' => $picture->getId()]);
    }

    #[Route('/picture/{id}', name: 'picture_delete', methods:"DELETE")]
    public function deletePicture(Picture $picture):JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($picture);
        $entityManager->flush();

        return $this->json(['message' => 'Picture deleted successfully']);
    }
}

