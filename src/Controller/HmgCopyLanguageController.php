<?php

namespace App\Controller;

use App\Repository\HmgCopyLanguageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HmgCopyLanguageController extends AbstractController
{
    public function __construct(
        private HmgCopyLanguageRepository $hmgCopyLanguageRepository
    ) {}

    #[Route('/hmgCopyLanguageAll', name: 'app_hmgcopylanguage', methods:"GET")]
    public function getAllLanguages(): Response
    {
        $copyLanguage = $this->hmgCopyLanguageRepository->findAll();

        return $this->json(['message' => 'good', 'result' => $copyLanguage], 200, [], ['groups' => 'copyLanguage:read']);
    }

}
