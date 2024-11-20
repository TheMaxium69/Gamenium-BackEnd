<?php

namespace App\Controller;

use App\Repository\HmgCopyFormatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HmgCopyFormatController extends AbstractController
{
    public function __construct(
        private HmgCopyFormatRepository $hmgCopyFormatRepository
    ) {}


    #[Route('/hmgCopyFormatAll', name: 'app_hmgcopyformat')]
    public function getAllHmgCopyFormat(): Response
    {

        $copyFormats = $this->hmgCopyFormatRepository->findAll();

        return $this->json(['message' => 'good', 'result' => $copyFormats], 200, [], ['groups' => 'copyFormat:read']);
    }

    #[Route('/hmgCopyFormat/{id}', name: 'app_hmgcopyformat_one')]
    public function getOneHmgCopyFormat(int $id): Response
    {
        $copyFormat = $this->hmgCopyFormatRepository->find($id);

        if (!$copyFormat) {
            return $this->json(['message' => 'hmgCopyFormat not found']);
        }

        return $this->json(['message' => 'good', 'result' => $copyFormat], 200, [], ['groups' => 'copyFormat:read']);
    }
}
