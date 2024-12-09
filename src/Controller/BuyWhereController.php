<?php

namespace App\Controller;

use App\Entity\BuyWhere;
use App\Entity\HmgCopyPurchase;
use App\Entity\User;
use App\Repository\BuyWhereRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuyWhereController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BuyWhereRepository $buywhere
    ) {}

    #[Route('/buywheres', name: 'all_buywhere', methods:"GET")]
    public function getAllBuyWheres():JSONResponse
    {
        $buywherePublic = $this->buywhere->findBy(['is_public' => true]);
        return $this->json(['message' => 'good', 'result' => $buywherePublic], 200, [], ['groups' => 'buywhere:read']);
    }

    #[Route('/buywherebyuser', name: 'all_buywhere_by_user', methods:"GET")]
    public function getAllBuyWheresByUser(Request $request):JSONResponse
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

            $buywhereUser = $this->buywhere->findBy(['user' => $user, 'is_public' => false]);
            $buywherePublic = $this->buywhere->findBy(['is_public' => true]);

            $i = 0;
            foreach ($buywhereUser as $buywhere) {

                $allPurchaseWithBuyWhere = $this->entityManager->getRepository(HmgCopyPurchase::class)->findBy(['buy_where'=>$buywhere]);

                $buywhereUser[$i]->setNbUse(count($allPurchaseWithBuyWhere));

                $i++;
            }

            $allBuywheres = array_merge($buywhereUser, $buywherePublic);

        }
        return $this->json(['message' => 'good', 'result' => $allBuywheres], 200, [], ['groups' => 'buywhereuser:read']);
    }

    #[Route('/createbuywhere/', name: 'create_buywere', methods:"POST")]
    public function createBuyWhere(Request $request): JSONResponse
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

            $buywhereDB = $this->buywhere->findBy(['user' => $user, 'name' => $data['name']]);
            if ($buywhereDB){
                return $this->json(['message' => 'BuyWhere already exist']);
            }


            $buywhere = new BuyWhere();
            $buywhere->setName($data['name']);
            $buywhere->setIp($newIp);
            $buywhere->setUser($user);
            $buywhere->setIsPublic(false);
            $buywhere->setCreatedAt(new \DateTimeimmutable());
            $this->entityManager->persist($buywhere);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $buywhere], 200, [], ['groups' => 'buywhere:read']);



        } else {
            return $this->json(['message' => 'Token invalide']);
        }

    }

    #[Route('/deletebuywhere/{id}', name: 'delete_buywhere', methods:"DELETE")]
    public function deleteBuyWhere(int $id, Request $request): JSONResponse
    {
        if (!$id) {
            return $this->json(['message' => 'No id']);
        }

        $buywhere = $this->buywhere->findOneBy(["id" => $id]);
        if (!$buywhere) {
            return $this->json(['message' => 'BuyWhere not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if(!$user){
                return $this->json(['message' => 'User invalide']);
            }

            if ($buywhere->getUser()->getId() !== $user->getId()) {
                return $this->json(['message' => 'no have permission']);
            }

            $purchase = $this->entityManager->getRepository(HmgCopyPurchase::class)->findBy(['buy_where' => $buywhere]);

            if ($purchase) {
                return $this->json(['message' => 'buywhere is use']);
            }

            $this->entityManager->remove($buywhere);
            $this->entityManager->flush();

            return $this->json(['message' => 'good']);

        } else {
            return $this->json(['message' => 'Token invalide']);
        }

    }



}

