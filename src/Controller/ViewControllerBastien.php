<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Entity\User;
use App\Entity\View;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('view')]
class ViewControllerBastien extends AbstractController
    
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
    )
    {}

    #[Route('-provider-add', name: 'app_view_provider_add', methods: ['POST'])]
    public function addProviderView(Request $request): JsonResponse
    {

        /*

        l'utilisateur de donne un id et une ip (et dans le beaure le token mais non obligatoire)
        ajoutez tout ça dans la table view

        */

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id_provider'])){
            return $this->json(['message' => 'undefine of field']);
        }

        /* SET UNE IP */
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
        }

        $idProvider = $data['id_provider'];

        /*SI LE PROVIDER EXISTE*/
        $provider = $this->entityManager->getRepository(Provider::class)->findOneBy(['id' => $idProvider]);
        if (!$provider){
            return $this->json(['message' => 'provider is failed']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            $view = new View();
            $view->setProvider($provider);
            $view->setWho($user);
            $view->setIp($newIp);
            $view->setViewAt(new \DateTimeImmutable());

            $this->entityManager->persist($view);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $view], 200, [], ['groups' => 'view:read']);

        } else {
            $view = new View();
            $view->setProvider($provider);
            $view->setIp($newIp);
            $view->setViewAt(new \DateTimeImmutable());

            $this->entityManager->persist($view);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $view], 200, [], ['groups' => 'view:read']);
        }

    }
}
