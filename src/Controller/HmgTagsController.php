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
                $allHmgWithTags = $this->hmgTagsRepository->countHmgWithTags($oneTags->getId());
                $tagsUser[$i]->setNbUse(count($allHmgWithTags));
                $i++;
            }

            $allTags = array_merge($tagsPublic, $tagsUser);

        }
        return $this->json(['message' => 'good', 'result' => $allTags], 200, [], ['groups' => 'hmgTags:read']);
    }

    #[Route('/createtag', name:'create_tag', methods:"POST")]
    public function createTag(Request $request): JSONResponse
    {
        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['name']) || trim($data['name']) === ""){
            return $this->json(['message' => 'undefine of field']);
        }


        if (!isset($data['color']) || trim($data['color']) === ""){
            return $this->json(['message' => 'undefine of field']);
        }

        /* SET UNE IP */
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if(!$user){
                return $this->json(['message' => 'User invalide']);
            }

            $tagDB = $this->hmgTagsRepository->findBy(['user' => $user, 'name' => $data['name']]);
            if ($tagDB){
                return $this->json(['message' => 'Tag already exist']);
            }

            $tag = new HmgTags();
            $tag->setName($data['name']);
            $tag->setIp($newIp);
            $tag->setUser($user);
            $tag->setColor($data['color']);
            $tag->setIsPublic(false);
            $tag->setCreatedAt(new \DateTimeimmutable());
            $this->entityManager->persist($tag);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $tag], 200, [], ['groups' => 'hmgTags:read']);



        } else {
            return $this->json(['message' => 'Token invalide']);
        }
    }

    #[Route('/deletetag/{id}', name: 'delete_tag', methods:"DELETE")]
    public function deleteTag(int $id, Request $request): JSONResponse
    {
        if (!$id) {
            return $this->json(['message' => 'No id']);
        }

        $tag = $this->hmgTagsRepository->findOneBy(["id" => $id]);
        if (!$tag) {
            return $this->json(['message' => 'Tag not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if(!$user){
                return $this->json(['message' => 'User invalide']);
            }

            if ($tag->getUser()->getId() !== $user->getId()) {
                return $this->json(['message' => 'no have permission']);
            }

            $nbUse = $this->hmgTagsRepository->countHmgWithTags($tag->getId());

            if($nbUse) {
                return $this->json(['message' => 'Tag is use']);
            }
            
            $this->entityManager->remove($tag);
            $this->entityManager->flush();

            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'Token invalide']);
        }

    }

}
