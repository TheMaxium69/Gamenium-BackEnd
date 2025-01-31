<?php

namespace App\Controller;

use App\Entity\User;
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

            if (!in_array('ROLE_ADMIN', $user->getRoles())) {
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
