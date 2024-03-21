<?php

namespace App\Controller;

use App\Entity\ProfilSocialNetwork;
use App\Entity\SocialNetwork;
use App\Repository\SocialNetworkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class SocialNetworkController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SocialNetworkRepository $socialNetworkRepository
    ) {}

        #[Route('/social-networks', name:'social_networks_submit', methods:['POST'])]

        public function submitForm(Request $request): Response
        {
            $data = json_decode($request->getContent(), true);
            // var_dump($data);

            /*SI LE JSON A PAS DE SOUCI */ 
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                return $this->json(['message' => 'Invalid JSON format']);
            }

            $authorizationHeader = $request->headers->get('Authorization');
            // var_dump($authorizationHeader);   
            
            if (strpos($authorizationHeader, 'Bearer ') === 0) {
                $token = substr($authorizationHeader, 7);

                /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
                if (!$user){
                    return $this->json(['message' => 'token is failed']);
                }

                // var_dump($user);

                $isCreated = false;
                $isUpdate = false;

                foreach($data as $value){

                    
                    /*SI LES CHAMP SON REMPLIE */
                    if (!empty($value['url']) && !empty($value['id_socialnetwork'])){


                        $socialNetwork = null;
                        $profilSocialNetwork = null;
                        $url = $value['url'];
                        // var_dump($value);

                        /*SI LE NEWORK EXISTE*/
                        $socialNetwork = $this->entityManager->getRepository(SocialNetwork::class)->findOneBy(['id' => $value['id_socialnetwork']]);
                        if ($socialNetwork){

                            /* si l'url existe déjà */
                            $profilSocialNetwork = $this->entityManager->getRepository(ProfilSocialNetwork::class)->findOneBy(['socialnetwork' => $socialNetwork, 'user'=>$user]);
                            if(!$profilSocialNetwork){
                            
                                // var_dump($socialNetwork);

                                $profilSocialNetwork = new ProfilSocialNetwork();
                                $profilSocialNetwork->setSocialnetwork($socialNetwork);
                                $profilSocialNetwork->setUser($user);
                                $profilSocialNetwork->setUrl($url);
                    
                                $this->entityManager->persist($profilSocialNetwork);
                                $this->entityManager->flush();
                                $isCreated = true;
                            
                            } else {
                                

                                $profilSocialNetwork->setUrl($url);
                                $this->entityManager->persist($profilSocialNetwork);
                                $this->entityManager->flush();
                                
                                $isUpdate = true;

                            }

                        }
                    
                    }

                }

                if($isUpdate && $isCreated){
                    return  $this->json(['message' => 'succefuly created and updated']);
                } else if($isUpdate){
                    return  $this->json(['message' => 'succefuly updated']);
                } else if($isCreated){
                    return  $this->json(['message' => 'succefuly created']);
                } else {
                    return  $this->json(['message' => 'err information']);
                }
            }

            
            return $this->json(['message' => 'no token']);


        }
}
