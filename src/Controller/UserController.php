<?php

namespace App\Controller;


use App\Entity\Picture;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $manager;
    private $user;
    private UserRepository $userRepository;


    public function __construct(EntityManagerInterface $manager, UserRepository $user, UserRepository $userRepository)
    {
        $this->manager = $manager;
        $this->user = $user;
        $this->userRepository = $userRepository;
    }



    #[Route('/login_user/', name: 'user_loggin', methods:"POST")]
    public function loginUser (Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // VERIFICATION DES CHAMP
        if (!isset($data['email_auth']) || !isset($data['mdp_auth'])) {

            return $this->json(['message' => 'Missing required fields']);

        }

        $ip = $request->getClientIp();
        if (!isset($ip)) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $ip;
        }


        // CONNECTION USERITIUM
        $emailForm = $data['email_auth'];
        $mdpForm = $data['mdp_auth'];

        $client = HttpClient::create();

        $url = 'https://useritium.fr/api-externe/index.php?controller=gamenium&task=connect';

        $body = [
            'email_useritium' => $emailForm,
            'mdp_useritium' => $mdpForm,
        ];

        $response = $client->request('POST', $url, [
            'body' => $body,
        ]);

        $content = $response->getContent();

        $resultUseritiumArray = json_decode($content, true);


        // VERIFICATION DU SERVEUR
        if(!$resultUseritiumArray){

            return $this->json(['message' => 'Erreur Base de donnée']);
        }


        // VERIFICATION DE CONNEXION
        if ($resultUseritiumArray['status'] == "err"){

            // PAS CONNECTER
            return $this->json(['message' => $resultUseritiumArray['why']]);

        } else if($resultUseritiumArray['status'] == "true"){

            // CONNECTER

            $user = $this->user->findOneBy(['id_useritium' => $resultUseritiumArray['result']['id']]);


            if ($user){

                // EDIT celui dans la db
                $user->setLastConnection(new \DateTimeImmutable());
                $user->setEmail($resultUseritiumArray['result']['email']);
                $user->setDisplaynameUseritium($resultUseritiumArray['result']['displayName']);
                $user->setUsername($resultUseritiumArray['result']['username']);
                    $ip = $user->getIp();
                    array_push($ip, $newIp);
                $user->setIp($ip);

                $this->manager->persist($user);
                $this->manager->flush();

            } else {


                // créer une db
                $user = new User();

                $user->setIdUseritium($resultUseritiumArray['result']['id']);
                    $role = [
                        "user"
                    ];
                $user->setUserRole($role);
                $user->setJoinAt(new \DateTimeImmutable());
                $user->setLastConnection(new \DateTimeImmutable());
//                $user->setIdPicture(1);
                    $ip = [
                        $newIp
                    ];
                $user->setIp($ip);
                $user->setEmail($resultUseritiumArray['result']['email']);
                $user->setDisplaynameUseritium($resultUseritiumArray['result']['displayName']);
                $user->setUsername($resultUseritiumArray['result']['username']);
                if ($resultUseritiumArray['result']['displayName']){
                    $user->setDisplayname($resultUseritiumArray['result']['displayName']);
                } else {
                    $user->setDisplayname($resultUseritiumArray['result']['username']);
                }
                    $randomToken = $this->generateRandomToken();
                $user->setToken($randomToken);

                $this->manager->persist($user);
                $this->manager->flush();

            }

            return $this->json(['message' => 'Connected', 'token' => $user->getToken()]);


        } else {

            // ERR API (NI ERR NI TRUE)
            return $this->json(['message' => 'Erreur Base de donnée']);

        }




    }

    #[Route('/login_token/', name: 'token_loggin', methods:"POST")]
    public function loginToken (Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // VERIFICATION DES CHAMP
        if (!isset($data['token'])) {

            return $this->json(['message' => 'Missing required fields']);

        } else {

            $token = $data['token'];

            $user = $this->user->findOneBy(['token' => $token]);


            if ($user){

                return $this->json(['message' => 'Connected', 'result' => [
                    "id" => $user->getId(),
                    "username" => $user->getUsername(),
                    "email" => $user->getEmail(),
                    "displayname" => $user->getDisplayname(),
                    "displaynameUseritium" => $user->getDisplaynameUseritium(),
                    "joinAt" => $user->getJoinAt(),
                    "userRole" => $user->getUserRole(),
                    "themeColor" => $user->getColor()
                ]]);

            } else {

                return $this->json(['message' => 'Token Invalide']);

            }


        }


    }

    #[Route('/get-theme-color/{userId}', name: 'get_theme_color', methods: ['GET'])]
    public function getThemeColor(int $userId): JsonResponse
    {

        $user = $this->userRepository->find($userId);
        $themeColor = $user ? $user->getColor() : '';
    
        return $this->json([$themeColor]);
    }

    #[Route('/update-theme-color/{userId}', name: 'update_theme_color', methods: ['POST'])]
    public function updateThemeColor(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, $userId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['themeColor'])) {
            return $this->json(['message' => 'Missing required fields'], 400);
        }

        $user = $userRepository->find($userId);
        if (!$user) {
            return $this->json(['message' => 'User not found'], 404);
        }

        $user->setColor($data['themeColor']); 
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'Theme color updated successfully']);

    }

    #[Route('/users/search', name: 'search_users', methods: ['POST'])]
    public function searchUsers(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $searchValue = $data['searchValue'] ?? '';
        $limit = $data['limit'];

        $results = $this->userRepository->searchUserByName($searchValue, $limit);
    
        return $this->json($results, 200, [], ['groups' => 'user:read']);
    }




    function generateRandomToken($length = 32)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charLength = strlen($characters);

        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $characters[random_int(0, $charLength - 1)];
        }

        return $token;
    }

}