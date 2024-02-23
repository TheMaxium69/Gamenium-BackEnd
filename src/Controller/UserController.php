<?php

namespace App\Controller;


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


    public function __construct(EntityManagerInterface $manager, UserRepository $user)
    {
        $this->manager = $manager;
        $this->user = $user;
    }



    #[Route('/user/', name: 'user_loggin', methods:"POST")]
    public function logginUser (Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // VERIFICATION DES CHAMP
        if (!isset($data['email_auth']) || !isset($data['mdp_auth'])) {

            return $this->json(['message' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);

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

            return $this->json(['message' => 'Erreur Base de donnée'], Response::HTTP_BAD_REQUEST);
        }


        // VERIFICATION DE CONNEXION
        if ($resultUseritiumArray['status'] == "err"){

            // PAS CONNECTER
            return $this->json(['message' => $resultUseritiumArray['why']], Response::HTTP_BAD_REQUEST);

        } else if($resultUseritiumArray['status'] == "true"){

            // CONNECTER


            //requete dans la db

            if ("SI IL EXISTE DEJA DANS NOTRE DB" == 1){

                // EDIT celui dans la db
                $user->setLastConnection(new \DateTimeImmutable());


            } else {


                // créer une db

                $user = new User();

                $user->setIdUseritium($resultUseritiumArray['result']['id']);
                $user->setJoinAt(new \DateTimeImmutable());
                $user->setLastConnection(new \DateTimeImmutable());
//                $user->setIp($data['ip']);

                $this->manager->persist($user);
                $this->manager->flush();

            }


            return $this->json(['message' => 'Usertium Test', 'result' => $resultUseritiumArray]);
//            return $this->json(['message' => 'Connected', 'token' => "41za5e4za65e4za5e5zaeaz4ea-azeza54eaz54eaz"]);


        } else {

            // ERR API (NI ERR NI TRUE)
            return $this->json(['message' => 'Erreur Base de donnée'], Response::HTTP_BAD_REQUEST);

        }




    }




}