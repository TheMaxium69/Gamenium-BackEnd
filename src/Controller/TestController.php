<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Test;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('test')]
class TestController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/getAll', name: 'test_all', methods: ['POST'])]
    public function getAllTest(Request $request): JsonResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_TEST_RESPONSABLE', 'ROLE_TEST'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }
            
            $data = json_decode($request->getContent(), true);
            $limit = $data['limit'];

            $results = $this->entityManager->getRepository(Test::class)->findAllWithLimit($limit);

            return $this->json($results, 200, [], ['groups' => 'testRate:read']);

        } else {

            return $this->json(['message' => 'no permission']);

        }

    }

    #[Route('/getbygame/{id}', name: 'test_by_game')]
    public function getTestByGame(int $id): JsonResponse
    {


        $game = $this->entityManager->getRepository(Game::class)->find($id);

        if (!$game) {
            return $this->json(['message' => 'game not found']);
        }


        $tests = $this->entityManager->getRepository(Test::class)->findBy(['game' => $game]);

        if (!$tests) {
            return $this->json(['message' => 'no tests found for this game']);
        }

        $AllTest = [];
        foreach ($tests as $testOne) {
            $user = $testOne->getUser();
            if (!in_array('ROLE_BAN', $user->getRoles())) {
                $AllTest[] = $testOne; // Stocker uniquement les replies sans User_Ban
            }
        }

        return $this->json(['message'=>'good','result' => $AllTest], 200, [], ['groups' => 'testRate:read']);
    }


    #[Route('/create', name: 'test_create', methods: ['POST'])]
    public function createTest(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['game_id']) || !isset($data['note'])){
            return $this->json(['message' => 'undefine of field']);
        }

        /* SET UNE IP */
        $ip = $request->getClientIp();
        if (!isset($ip)) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $ip;
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /* VERIFIER L'USER*/
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if(!$user){
                return $this->json(['message' => 'User invalide']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_TEST_RESPONSABLE', 'ROLE_TEST'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            /*SI LA NOTE EST AU DESSUS DE 20*/
            if (filter_var($data['note'], FILTER_VALIDATE_INT) !== false && $data['note'] >= 0 && $data['note'] <= 20) {

                $noteValide = $data['note'];

            } else {
                return $this->json(['message' => 'note no valide']);
            }

            /* VERIFIER LE JEUX*/
            $game = $this->entityManager->getRepository(Game::class)->find($data['game_id']);

            if (!$game) {
                return $this->json(['message' => 'game not found']);
            }
            
            $test = new Test();
            $test->setRating($noteValide);
            $test->setIp($newIp);
            $test->setUser($user);
            $test->setGame($game);
            $test->setTestAt(New \DateTimeImmutable());
            $test->setContent($data['content']);

            $this->entityManager->persist($test);
            $this->entityManager->flush();

            return $this->json(['message' => 'good', 'result' => $test], 200, [], ['groups' => 'testRate:read']);

        } else {
            return $this->json(['message' => 'Token invalide']);
        }
    }


    #[Route('/delete/{id}', name: 'test_delete', methods: ['DELETE'])]
    public function deleteTest(int $id, Request $request): JsonResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');

        // Vérification du token
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            // Recherche de l'utilisateur via le token
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'User invalide']);
            }

            // Récupération du test à supprimer
            $test = $this->entityManager->getRepository(Test::class)->find($id);
            if (!$test) {
                return $this->json(['message' => 'Test not found']);
            }

            // Vérification des permissions
            if ($test->getUser() === $user || in_array(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_TEST_RESPONSABLE'], $user->getRoles(), true)) {
                $this->entityManager->remove($test);
                $this->entityManager->flush();

                return $this->json(['message' => 'Test deleted successfully']);
            } else {
                return $this->json(['message' => 'no permission']);
            }
        } else {
            return $this->json(['message' => 'Token invalide']);
        }
    }


    #[Route('/update/{id}', name: 'test_update', methods: ['PUT'])]
    public function updateTest(int $id, Request $request): JsonResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');

        // Vérification du token
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            // Recherche de l'utilisateur via le token
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            if (!$user) {
                return $this->json(['message' => 'User invalide']);
            }

            // Récupération du test à mettre à jour
            $test = $this->entityManager->getRepository(Test::class)->find($id);
            if (!$test) {
                return $this->json(['message' => 'Test not found']);
            }

            // Vérification de la permission : seul le créateur peut mettre à jour
            if ($test->getUser() !== $user) {
                return $this->json(['message' => 'no permission']);
            }

            // Récupération des données soumises
            $data = json_decode($request->getContent(), true);

            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                return $this->json(['message' => 'Invalid JSON format']);
            }

            // Application des changements au Test s'ils sont valides
            if (isset($data['note']) && filter_var($data['note'], FILTER_VALIDATE_INT) !== false && $data['note'] >= 0 && $data['note'] <= 20) {
                $test->setRating($data['note']);
            } else {
                return $this->json(['message' => 'note no valide']);
            }

            if (isset($data['content'])) {
                $test->setContent($data['content']);
            }

            $this->entityManager->flush();

            return $this->json(['message' => 'Test updated successfully', 'result' => $test], 200, [], ['groups' => 'testRate:read']);
        } else {
            return $this->json(['message' => 'Token invalide']);
        }
    }
    
    
    
}
