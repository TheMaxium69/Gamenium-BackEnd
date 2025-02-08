<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Entity\BadgeVersUser;
use App\Entity\Comment;
use App\Entity\CommentReply;
use App\Entity\Game;
use App\Entity\HistoryMyGame;
use App\Entity\HistoryMyPlateform;
use App\Entity\Log;
use App\Entity\LogRole;
use App\Entity\Picture;
use App\Entity\PostActu;
use App\Entity\ProfilSocialNetwork;
use App\Entity\Provider;
use App\Entity\User;
use App\Entity\UserRate;
use App\Entity\View;
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

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO', 'ROLE_WRITE_RESPONSABLE', 'ROLE_TEST_RESPONSABLE'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $group = 'usermodo:read';
            if (array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
                $group = 'useradmin:read';
            }
            

            $data = json_decode($request->getContent(), true);
            $searchValue = $data['searchValue'] ?? '';
            $limit = $data['limit'];

            $results = $this->entityManager->getRepository(User::class)->searchUserByName($searchValue, $limit);

            return $this->json($results, 200, [], ['groups' => $group]);
            
            
            

        } else {

            return $this->json(['message' => 'no permission']);

        }

    }

    #[Route('-profils-search', name: 'search_profils_admin', methods: ['POST'])]
    public function searchProfils(Request $request): JsonResponse
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

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }


            $data = json_decode($request->getContent(), true);
            $searchValue = $data['searchValue'] ?? '';
            $limit = $data['limit'];


            $userAll = $this->entityManager->getRepository(User::class)->searchUserByName($searchValue, $limit);

            $userResult = [];
            foreach ($userAll as $userOne) {

                $badgeVersUser = $this->entityManager->getRepository(BadgeVersUser::class)->findBy(['user' => $userOne]);
                $userBadges = [];

                if ($badgeVersUser) {
                    foreach ($badgeVersUser as $badge) {
                        $userBadges[] = [
                            'id' => $badge->getBadge()->getId(),
                            "name" => $badge->getBadge()->getName(),
                            "pictureUrl" => $badge->getBadge()->getPicture()->getUrl()
                        ];
                    }
                }

                if ($userOne->getPp() !== null) {
                    $picture = [
                        'id' => $userOne->getPp()->getId(),
                        'url' => $userOne->getPp()->getUrl()
                    ];
                } else {
                    $picture = null;
                }

                $userResult[] = [
                    "id" => $userOne->getId(),
                    "username" => $userOne->getUsername(),
                    "displayname" => $userOne->getDisplayname(),
                    "displayname_useritium" => $userOne->getDisplaynameUseritium(),
                    "color" => $userOne->getColor(),
                    "pp" => $picture,
                    "roles" => $userOne->getRoles(),
                    "badges" => $userBadges,
                ];
            }




            return $this->json($userResult, 200, []);




        } else {

            return $this->json(['message' => 'no permission']);

        }

    }

    #[Route('-postactus-search', name: 'search_postactus_admin', methods: ['POST'])]
    public function searchPostActuAdmin(Request $request): JsonResponse
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

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $data = json_decode($request->getContent(), true);
            $searchValue = $data['searchValue'] ?? '';
            $limit = $data['limit'];

            $results = $this->entityManager->getRepository(PostActu::class)->searchPostActuByNameWithView($searchValue, $limit);

            $finalResults = [];
            foreach ($results as $onePostActu) {

                /* JSON */
                $onePostActu['picture'] = $this->entityManager->getRepository(Picture::class)->findOneBy(['id' => $onePostActu['picture_id']]);
                $onePostActu['Provider'] = $this->entityManager->getRepository(Provider::class)->findOneBy(['id' => $onePostActu['provider_id']]);

                /* nameVariable */
                $onePostActu = array_merge($onePostActu, [
                    'picture' => $onePostActu['picture'],
                    'Provider' => $onePostActu['Provider'],
                ]);

                $finalResults[] = $onePostActu;
            }

            return $this->json($finalResults, 200, [], ['groups' => 'postactu:read']);

        } else {

            return $this->json(['message' => 'no token']);

        }
    }

    #[Route('-provider-search', name: 'search_provider_admin', methods: ['POST'])]
    public function searchProviderAdmin(Request $request): JsonResponse
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

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $data = json_decode($request->getContent(), true);
            $searchValue = $data['searchValue'] ?? '';
            $limit = $data['limit'];

            $results = $this->entityManager->getRepository(Provider::class)->searchProviderByNameWithView($searchValue, $limit);

            $finalResults = [];
            foreach ($results as $oneProvider) {

                /* JSON */
                $oneProvider['picture'] = $this->entityManager->getRepository(Picture::class)->findOneBy(['id' => $oneProvider['picture_id']]);

                /* nameVariable */
                $oneProvider = array_merge($oneProvider, [
                    'picture' => $oneProvider['picture'],
                ]);

                $finalResults[] = $oneProvider;
            }

            return $this->json($finalResults, 200, [], ['groups' => 'postactu:read']);
        } else {
            return $this->json(['message' => 'no token']);
        }
    }

    #[Route('-profilview-search', name: 'search_profil_view_admin', methods: ['POST'])]
    public function searchProfilViewAdmin(Request $request): JsonResponse
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

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }

            $data = json_decode($request->getContent(), true);
            $searchValue = $data['searchValue'] ?? '';
            $limit = $data['limit'];

            $results = $this->entityManager->getRepository(User::class)->searchProfilByNameWithView($searchValue, $limit);

            $finalResults = [];
            foreach ($results as $oneProfil) {

                /* JSON */
                $picture = $this->entityManager->getRepository(Picture::class)->findOneBy(['id' => $oneProfil['pp_id']]);
                $nbView = $this->entityManager->getRepository(View::class)->count(['profile' => $oneProfil['id']]);

                $userResult = [
                    "id" => $oneProfil['id'],
                    "username" => $oneProfil['username'],
                    "displayname" => $oneProfil['displayname'],
                    "displayname_useritium" => $oneProfil['displayname_useritium'],
                    "color" => $oneProfil['color'],
                    "pp" => $picture,
                    "nbView" => $nbView,
                    "roles" => json_decode($oneProfil['roles'])
                ];

                $finalResults[] = $userResult;
            }

            return $this->json($finalResults, 200, [], ['groups' => 'user:read']);
        } else {
            return $this->json(['message' => 'no token']);
        }
    }

    #[Route('-games-search', name: 'search_games_admin', methods: ['POST'])]
    public function searchGamesAdmin(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $searchValue = $data['searchValue'] ?? '';
        $limit = $data['limit'];

        $results = $this->entityManager->getRepository(Game::class)->searchByName($searchValue, $limit);

        $finalResults = [];
        foreach($results as $oneGame){

            /* JSON */
            $oneGame['image'] = json_decode($oneGame['image']);
            $oneGame['imageTags'] = json_decode($oneGame['image_tags']);
            $oneGame['originalGameRating'] = json_decode($oneGame['original_game_rating']);
            $oneGame['platforms'] = json_decode($oneGame['platforms']);
            $oneGame['moyenRateUser'] = $this->entityManager->getRepository(UserRate::class)->calcMoyenByGame($oneGame['id']);

            /* nameVariable */
            $oneGame = array_merge($oneGame, [
                'dateLastUpdated' => $oneGame['date_last_updated'],
                'expectedReleaseDay' => $oneGame['expected_release_day'],
                'expectedReleaseMonth' => $oneGame['expected_release_month'],
                'expectedReleaseYear' => $oneGame['expected_release_year'],
                'id_GiantBomb' => $oneGame['id_giant_bomb'],
                'siteDetailUrl' => $oneGame['site_detail_url'],
                'originalReleaseDate' => $oneGame['original_release_date'],
                'numberOfUserReviews' => $oneGame['number_of_user_reviews'],
            ]);




            $finalResults[] = $oneGame;
        }

        return $this->json($finalResults, 200, []);
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
            !in_array('ROLE_MODO', $user->getRoles()) &&
            !in_array('ROLE_WRITE_RESPONSABLE', $user->getRoles()) &&
            !in_array('ROLE_WRITE_SUPER', $user->getRoles()) &&
            !in_array('ROLE_WRITE', $user->getRoles()) &&
            !in_array('ROLE_TEST_RESPONSABLE', $user->getRoles()) &&
            !in_array('ROLE_TEST', $user->getRoles()) &&
            !in_array('ROLE_PROVIDER_ADMIN', $user->getRoles()) &&
            !in_array('ROLE_PROVIDER', $user->getRoles()) )
            {
                return $this->json(['message' => 'no permission']);
            }

            // Une fois qu'on sait qu'il a les permissions on renvoit le profil signalé
            $profilSocialNetworks = $this->entityManager->getRepository(ProfilSocialNetwork::class)->findBy(['user' => $userSearched]);
            $historyMyGames = $this->entityManager->getRepository(HistoryMyGame::class)->findBy(['user' => $userSearched]);
            $userRates = $this->entityManager->getRepository(UserRate::class)->findBy(['user' => $userSearched]);
            $badgeVersUser = $this->entityManager->getRepository(BadgeVersUser::class)->findBy(['user' => $userSearched]);
            $nbSanction = $this->entityManager->getRepository(Log::class)->count(['user' => $userSearched]);

            $userBadges = [];
            foreach ($badgeVersUser as $badge) {
                $userBadges[] = [
                    'id' => $badge->getBadge()->getId(),
                    "name" => $badge->getBadge()->getName(),
                    "pictureUrl" => $badge->getBadge()->getPicture()->getUrl()
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
                    "nbSanction" => $nbSanction,
                    "nbView" => $this->entityManager->getRepository(View::class)->count(['profile' => $userSearched]),
                ]
            ];

            return $this->json($message, 200, [], ['groups' => 'profilSocialNetwork:read']);
        }

        return $this->json(['message' => 'Token invalide']);

    }

    #[Route('-comment-search', name: 'search_comment_admin', methods: ['POST'])]
    public function searchComment(Request $request): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }


            $data = json_decode($request->getContent(), true);
            $searchValue = $data['searchValue'] ?? '';
            $limit = $data['limit'];

            $comments = $this->entityManager->getRepository(Comment::class)->searchComment($searchValue, $limit);

            return $this->json($comments, 200, [], ['groups' => 'comment:admin']);
            
            

        } else {

            return $this->json(['message' => 'Token invalide']);

        }

    }

    #[Route('-comment-reply-search', name: 'search_comment_reply_admin', methods: ['POST'])]
    public function searchCommentReply(Request $request): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }


            $data = json_decode($request->getContent(), true);
            $searchValue = $data['searchValue'] ?? '';
            $limit = $data['limit'];

            $commentsreply = $this->entityManager->getRepository(CommentReply::class)->searchCommentReply($searchValue, $limit);

            return $this->json($commentsreply, 200, [], ['groups' => 'commentreply:admin']);



        } else {

            return $this->json(['message' => 'Token invalide']);

        }

    }

    #[Route('-hmg-search', name: 'search_hmg_admin', methods: ['POST'])]
    public function searchHmg(Request $request): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }


            $data = json_decode($request->getContent(), true);
            $searchValue = $data['searchValue'] ?? '';
            $limit = $data['limit'];

            $hmgs = $this->entityManager->getRepository(HistoryMyGame::class)->searchHmg($searchValue, $limit);

            return $this->json($hmgs, 200, [], ['groups' => 'historygame:read']);



        } else {

            return $this->json(['message' => 'Token invalide']);

        }

    }
    #[Route('-hmp-search', name: 'search_hmp_admin', methods: ['POST'])]
    public function searchHmp(Request $request): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        /*SI LE TOKEN EST REMPLIE */
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            $token = substr($authorizationHeader, 7);

            /*SI LE TOKEN A BIEN UN UTILISATEUR EXITANT */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

            if (!$user) {
                return $this->json(['message' => 'no permission']);
            }

            if (!array_intersect(['ROLE_ADMIN', 'ROLE_OWNER', 'ROLE_MODO_RESPONSABLE', 'ROLE_MODO_SUPER', 'ROLE_MODO'], $user->getRoles())) {
                return $this->json(['message' => 'no permission']);
            }


            $data = json_decode($request->getContent(), true);
            $searchValue = $data['searchValue'] ?? '';
            $limit = $data['limit'];

            $hmps = $this->entityManager->getRepository(HistoryMyPlateform::class)->searchHmp($searchValue, $limit);

            return $this->json($hmps, 200, [], ['groups' => 'historyplateform:read']);



        } else {

            return $this->json(['message' => 'Token invalide']);

        }

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
    #[Route('-role-one', name: 'role_one', methods: ['POST'])]
    public function getRoleOne(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        /*SI LE JSON A PAS DE SOUCI */
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        /*SI LES CHAMP SON REMPLIE */
        if (!isset($data['ROLE_NAME'])){
            return $this->json(['message' => 'undefine of field']);
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

            //on vérifie que le user a bien l'un des roles
            if (!in_array('ROLE_OWNER', $user->getRoles()) &&
                !in_array('ROLE_ADMIN', $user->getRoles()) &&
                !in_array('ROLE_MODO_RESPONSABLE', $user->getRoles()) &&
                !in_array('ROLE_MODO_SUPER', $user->getRoles()) &&
                !in_array('ROLE_MODO', $user->getRoles()) &&
                !in_array('ROLE_WRITE_RESPONSABLE', $user->getRoles()) &&
                !in_array('ROLE_WRITE_SUPER', $user->getRoles()) &&
                !in_array('ROLE_WRITE', $user->getRoles()) &&
                !in_array('ROLE_TEST_RESPONSABLE', $user->getRoles()) &&
                !in_array('ROLE_TEST', $user->getRoles()) &&
                !in_array('ROLE_PROVIDER_ADMIN', $user->getRoles()) &&
                !in_array('ROLE_PROVIDER', $user->getRoles()) ) {
                return $this->json(['message' => 'no permission']);
            }

            $limit = $data['limit'];
            $roleName = $data['ROLE_NAME'];

            $userMaybe = $this->entityManager->getRepository(User::class)->searchRoleByUser($roleName, $limit);

            $results = [];
            foreach ($userMaybe as $userOne) {
                foreach ($userOne->getRoles() as $role) {
                    if ($role === $roleName) {
                        $results[] = $userOne;
                    }
                }
            }

            return $this->json($results, 200, [], ['groups' => 'usermodo:read']);

        } else {
            return $this->json(['message' => 'no token']);
        }
    }


    #[Route('-ban/{id}', name: 'ban_admin', methods: ['GET'])]
    public function toggleBan(Request $request, int $id): JsonResponse
    {

        $pendingUser = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
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

            //on vérifie que le user a bien l'un des roles
            if (!in_array('ROLE_OWNER', $user->getRoles()) &&
                !in_array('ROLE_ADMIN', $user->getRoles()) &&
                !in_array('ROLE_MODO_RESPONSABLE', $user->getRoles()) &&
                !in_array('ROLE_MODO_SUPER', $user->getRoles()) &&
                !in_array('ROLE_MODO', $user->getRoles()))
            {
                return $this->json(['message' => 'no permission']);
            }

            $roles = $pendingUser->getRoles();
            $roles = array_values(array_filter($roles, fn($role) => $role !== 'ROLE_USER'));

            // on verifie si le moderateur peut ban

            $canBan = $this->canBanRole($user->getRoles(), $roles);
            if (!$canBan) {
                return $this->json(['message' => 'your role have not permission']);
            }

            //on vérifie que le user a bien l'un des roles
            if (in_array('ROLE_BAN', $roles))
            {
                /* IL DEJA EST BAN*/
                $roles = array_values(array_filter($roles, fn($role) => $role !== 'ROLE_BAN'));
                $this->createLogRole($pendingUser, 'ROLE_BAN', 'remove', $user);

            } else {
                /* IL N'EST PAS BAN */
                $roles[] = 'ROLE_BAN';
                $this->createLogRole($pendingUser, 'ROLE_BAN', 'add', $user);

                /* FOR LOG */
                $newLog = new Log();
                $newLog->setWhy("BAN USER");
                $newLog->setUser($pendingUser);
                $newLog->setModeratedBy($user);
                $newLog->setCreatedAt(new \DateTimeImmutable());
                $this->entityManager->persist($newLog);
                $this->entityManager->flush();
                /* FOR LOG */
            }

            $pendingUser->setRoles($roles);
            $this->entityManager->persist($pendingUser);
            $this->entityManager->flush();

            return $this->json(['message' => 'toggle ban successfully']);

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
//                    'ROLE_BAN',
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


    
    function canBanRole($rolesModerateur, $rolesUser){
        
        $canManageRole = $this->canManageRole($rolesModerateur);
        if (in_array('ROLE_MODO', $rolesModerateur, true) || in_array('ROLE_MODO_SUPER', $rolesModerateur, true)) {
            $canManageRole[] = 'ROLE_BETA';
        }

        foreach ($rolesUser as $role) {
            if (!in_array($role, $canManageRole, true) && $role !== 'ROLE_BAN') {
                return false;
            }
        }
        return true;
        
    }












}
