<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Entity\BadgeVersUser;
use App\Entity\HistoryMyGame;
use App\Entity\Log;
use App\Entity\LogRole;
use App\Entity\ProfilSocialNetwork;
use App\Entity\User;
use App\Entity\UserRate;
use App\Repository\PostActuRepository;
use App\Repository\UserRepository;
use App\Repository\ViewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Amp\Iterator\toArray;

#[Route('admin')]
class AdministrationController extends AbstractController
{


    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}



    #[Route('-users-search', name: 'search_users_admin', methods: ['POST'])]
    public function searchUsers(Request $request): JsonResponse
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

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_WRITE_RESPONSABLE', 'ROLE_TEST_RESPONSABLE'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }
            

            $data = json_decode($request->getContent(), true);
            $searchValue = $data['searchValue'] ?? '';
            $limit = $data['limit'];

            $results = $this->entityManager->getRepository(User::class)->searchUserByName($searchValue, $limit);

            return $this->json($results, 200, [], ['groups' => 'useradmin:read']);
            
            
            

        } else {

            return $this->json(['message' => 'no permission']);

        }

    }


    // Get Profil User By Id
    #[Route('-profil/{id}', name: 'one_profil', methods:['GET'])]
    public function getProfilById(Request $request, int $id): JsonResponse {

        $authorizationHeader = $request->headers->get('Authorization');

        if (!$id) {
            return $this->json(['message' => 'id not found']);
        }

        //on récupère l'utilisateur recherché
        $userSearched = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        
        if(!$userSearched) {
            return $this->json(['message' => 'user not found']);
        }

        //On vérifie que le token n'est pas vide
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
            
            //on verifie que le user existe
            if (!$user){
                return $this->json(['message' => 'token is failed']);
            }

            //on vérifie que le user a bien l'un des roles
            if (!in_array('ROLE_OWNER', $user->getRoles()) &&
            !in_array('ROLE_ADMIN', $user->getRoles()) && 
            !in_array('ROLE_MODO_RESPONSABLE', $user->getRoles()) && 
            !in_array('ROLE_MODO_SUPER', $user->getRoles()) && 
            !in_array('ROLE_MODO', $user->getRoles())) 
            {
                return $this->json(['message' => 'no permission']);
            }

            // Une fois qu'on sait qu'il a les permissions on renvoit le profil signalé
            $profilSocialNetworks = $this->entityManager->getRepository(ProfilSocialNetwork::class)->findBy(['user' => $userSearched]);
            $historyMyGames = $this->entityManager->getRepository(HistoryMyGame::class)->findBy(['user' => $userSearched]);
            $userRates = $this->entityManager->getRepository(UserRate::class)->findBy(['user' => $userSearched]);
            $badgeVersUser = $this->entityManager->getRepository(BadgeVersUser::class)->findBy(['user' => $userSearched]);
            $signal = $this->entityManager->getRepository(Log::class)->findBy(['user' => $userSearched]);


            $nbSignal = count($signal);

            $userBadges = [];
            foreach ($badgeVersUser as $badge) {
                $badgeName = $badge->getBadge()->getName();
                $badgePicture = $badge->getBadge()->getPicture()->getUrl();
                $userBadges[] = [
                    "name" => $badgeName,
                    "pictureUrl" => $badgePicture
                ];
            }
            
            if ($userSearched->getPp() !== null) {
                $picture = $userSearched->getPp()->getUrl();
            } else {
                $picture = null;
            }

            if ($userSearched->getColor() !== null) {
                $color = $userSearched->getColor();
            } else {
                $color = null;
            }

            if (in_array('ROLE_OWNER', $user->getRoles()) ||
            in_array('ROLE_ADMIN', $user->getRoles())) {
                $ipUsed = $userSearched->getIp();
            } else {
                $ipUsed = "no permission";
            }



            $message = [
                'message' => "good",
                'result' => [
                    "id" => $userSearched->getId(),
                    "username" => $userSearched->getUsername(),
                    "displayname" => $userSearched->getDisplayname(),
                    "displaynameUseritium" => $userSearched->getDisplaynameUseritium(),
                    "joinAt" => $userSearched->getJoinAt(),
                    "lastConnection" => $userSearched->getLastConnection(),
                    "nbConnection" => count($userSearched->getIp()),
                    "ipUsed" => $ipUsed,
                    "themeColor" => $color,
                    "picture" => $picture,
                    "nbGame" => count($historyMyGames),
                    "nbNote" => count($userRates),
                    "reseau" => $profilSocialNetworks,
                    "roles" => $userSearched->getRoles(),
                    "badges" => $userBadges,
                    "nbSignal" => $nbSignal
                ]
            ];

            return $this->json($message, 200, [], ['groups' => 'profilSocialNetwork:read']);       
        }

        return $this->json(['message' => 'Token invalide']);

    }












    /*
     *
     *
     *
     *
     *
     *
     *  GESTION DES ROLES
     *
     *
     *
     *
     *
     * */




    #[Route('-add-role', name: 'add_role_admin', methods: ['POST'])]
    public function addRole(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id_user']) && !isset($data['new_role'])){
            return $this->json(['message' => 'undefine of field']);
        }

        $pendingUser = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['id_user']]);
        if (!$pendingUser) {
            return $this->json(['message' => 'user not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'user token not found']);
            }

            $canAddRole = $this->canManageRole($user->getRoles());

            if (in_array($data['new_role'], $canAddRole, true)) {

                $roles = $pendingUser->getRoles();

                $roles = array_values(array_filter($roles, fn($role) => $role !== 'ROLE_USER'));

                if (!in_array($data['new_role'], $roles, true)) {
                    $roles[] = $data['new_role'];
                }
                
                $pendingUser->setRoles($roles);
                $this->entityManager->persist($pendingUser);
                $this->entityManager->flush();

                $this->createLogRole($pendingUser, $data['new_role'], 'add', $user);

                return $this->json(['message' => 'Role added successfully']);

            } else {

                return $this->json(['message' => 'Invalid role or no permission']);
            }
            
        } else {

            return $this->json(['message' => 'no permission']);

        }
    }






    #[Route('-remove-role', name: 'remove_role_admin', methods: ['POST'])]
    public function removeRole(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['id_user']) && !isset($data['remove_role'])){
            return $this->json(['message' => 'undefine of field']);
        }

        $pendingUser = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['id_user']]);
        if (!$pendingUser) {
            return $this->json(['message' => 'user not found']);
        }

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT - SINON C PAS GRAVE SA SERA ANNONYME */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'user token not found']);
            }

            $canAddRole = $this->canManageRole($user->getRoles());


            if (in_array($data['remove_role'], $canAddRole, true)) {

                $roles = $pendingUser->getRoles();

                $roles = array_values(array_filter($roles, fn($role) => $role !== 'ROLE_USER'));

                if (in_array($data['remove_role'], $roles, true)) {

                    $roles = array_values(array_filter($roles, fn($role) => $role !== $data['remove_role']));

                    $pendingUser->setRoles($roles);
                    $this->entityManager->persist($pendingUser);
                    $this->entityManager->flush();

                    $this->createLogRole($pendingUser, $data['remove_role'], 'remove', $user);

                    return $this->json(['message' => 'Role remove successfully']);

                } else {

                    return $this->json(['message' => 'Role not found']);

                }

            } else {

                return $this->json(['message' => 'Invalid role or no permission']);
            }


        } else {

            return $this->json(['message' => 'no permission']);

        }
    }

    function createLogRole($pending_user, $role_update, $role_action, $user_action)
    {

        $logRole = new LogRole();
        $logRole->setUser($pending_user);
        $logRole->setRole($role_update);
        $logRole->setAction($role_action);
        $logRole->setActionBy($user_action);
        $logRole->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($logRole);
        $this->entityManager->flush();

    }





    function canManageRole($rolesUser)
    {

        $canManageRoleArray = [];

        foreach ($rolesUser as $role) {

            /*
             *
             * ADMINISTRATION
             *
             * */

            if ($role === 'ROLE_OWNER') {
                $canManageRoleArray = array_merge($canManageRoleArray, [
                    'ROLE_ADMIN',

                    'ROLE_MODO_RESPONSABLE',
                    'ROLE_MODO_SUPER',
                    'ROLE_MODO',

                    'ROLE_WRITE_RESPONSABLE',
                    'ROLE_WRITE_SUPER',
                    'ROLE_WRITE',

                    'ROLE_TEST_RESPONSABLE',
                    'ROLE_TEST',

                    'ROLE_PROVIDER_ADMIN',
                    'ROLE_PROVIDER',

                    'ROLE_BETA',
                    'ROLE_BAN',
                ]);
            }

            if ($role === 'ROLE_ADMIN') {
                $canManageRoleArray = array_merge($canManageRoleArray, [
                    'ROLE_MODO_RESPONSABLE',
                    'ROLE_MODO_SUPER',
                    'ROLE_MODO',

                    'ROLE_WRITE_RESPONSABLE',
                    'ROLE_WRITE_SUPER',
                    'ROLE_WRITE',

                    'ROLE_TEST_RESPONSABLE',
                    'ROLE_TEST',

                    'ROLE_PROVIDER_ADMIN',
                    'ROLE_PROVIDER',

                    'ROLE_BETA',
                    'ROLE_BAN',
                ]);
            }

            /*
             *
             * MODERATION
             *
             * */
            if ($role === 'ROLE_MODO_RESPONSABLE') {
                $canManageRoleArray = array_merge($canManageRoleArray, [
                    'ROLE_MODO_SUPER',
                    'ROLE_MODO',
                ]);
            }


            /*
             *
             * WRITE
             *
             * */
            if ($role === 'ROLE_WRITE_RESPONSABLE') {
                $canManageRoleArray = array_merge($canManageRoleArray, [
                    'ROLE_WRITE_SUPER',
                    'ROLE_WRITE',
                ]);
            }


            /*
             *
             * TEST
             *
             * */
            if ($role === 'ROLE_TEST_RESPONSABLE') {
                $canManageRoleArray = array_merge($canManageRoleArray, [
                    'ROLE_TEST',
                ]);
            }



        }

        return $canManageRoleArray;

    }














}
