<?php

namespace App\Controller;

use App\Entity\HistoryMyGame;
use App\Entity\HmgCopyPurchase;
use App\Entity\HmgTags;
use App\Entity\User;
use App\Repository\HmgTagsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HmgTagsController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private HmgTagsRepository $hmgTagsRepository,
    ) {}

    #[Route('/tagsbyuser', name: 'all_tags_by_user', methods:"GET")]
    public function getTagsAllByUser(Request $request):JSONResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /* SI L'UTILISATEUR CORRESPOND AU TOKEN  */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'Token invalide']);
            }

            $tagsUser = $this->hmgTagsRepository->findBy(['user' => $user, 'is_public' => false]);
            $tagsPublic = $this->hmgTagsRepository->findBy(['is_public' => true]);

            $i = 0;
            foreach ($tagsUser as $oneTags) {
                $countTags = $this->hmgTagsRepository->countHmgWithTags($oneTags->getId());
//                var_dump($countTags);
                foreach ($countTags as $oneTag) {
                    var_dump($oneTag->getId());
                }
                $tagsUser[$i]->setNbUse(count($countTags));
                $i++;
            }

            $allTags = array_merge($tagsPublic, $tagsUser);

        }
        return $this->json(['message' => 'good', 'result' => $allTags], 200, [], ['groups' => 'hmgTags:read']);
    }
}
