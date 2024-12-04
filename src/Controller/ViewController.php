<?php

namespace App\Controller;

use App\Entity\PostActu;
use App\Entity\User;
use App\Entity\View;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('view')]
class ViewController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    #[Route('-actu-add', name: 'app_view_actu_add', methods: ['POST'])]
    public function addActuView(Request $request): Response
    {

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id'])){
            return $this->json(['message' => 'undefine of field']);
        }

        /* SET UNE IP */
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
        }

        $idPostActu = $data['id'];

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        }

        /*SI L'ACTU EXISTE*/
        $postActu = $this->entityManager->getRepository(PostActu::class)->findOneBy(['id' => $idPostActu]);
        if (!$postActu) {
            return $this->json(['message' => 'actu is failed']);
        }

        $view = new View();
        $view->setPostActu($postActu);
        if ($user) {
            $view->setWho($user);
        }
        $view->setIp($newIp);
        $view->setViewAt(new \DateTimeImmutable());

        $this->entityManager->persist($view);
        $this->entityManager->flush();

        return $this->json(['message' => 'good', 'result' => $view], 200, [], ['groups' => 'view:read']);

    }
}
