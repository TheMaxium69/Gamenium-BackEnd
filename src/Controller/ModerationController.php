<?php

namespace App\Controller;

use App\Entity\Log;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('modo')]
class ModerationController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('-exemple', name: 'app_moderation')]
    public function exemple(): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $moderated = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$moderated) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $moderated->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }





            /* FOR LOG */
            $newLog = new Log();
            $newLog->setWhy("BAN USER");
            $newLog->setUser(/* SET L'UTILISATEUR CONCERNER */);
            $newLog->setModeratedBy($moderated);
            $newLog->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($newLog);
            $this->entityManager->flush();
            /* FOR LOG */





            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'no token']);
        }

    }
}
