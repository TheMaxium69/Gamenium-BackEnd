<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('view')]
class ViewControllerBastien extends AbstractController
{
    #[Route('-provider-add', name: 'app_view_provider_add', methods: ['POST'])]
    public function addProviderView(): Response
    {

        /*

        l'utilisateur de donne un id et une ip (et dans le beaure le token mais non obligatoire)
        ajoutez tout ça dans la table view

        */






        return $this->json('');
    }
}
