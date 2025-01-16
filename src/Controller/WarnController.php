<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\CommentReply;
use App\Entity\HistoryMyGame;
use App\Entity\HistoryMyPlateform;
use App\Entity\PostActu;
use App\Entity\User;
use App\Entity\Warn;
use App\Entity\WarnType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WarnController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}


    #[Route('/addwarn', name: 'add_warn', methods: ['POST'])]
    public function addWarn(Request $request): JsonResponse
    {
            
        $data = json_decode($request->getContent(), true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['message' => 'Invalid JSON format']);
        }

        if (!isset($data['warn_type_id'])){
            return $this->json(['message' => 'undefine of field']);
        }

        if (isset($data['profil_id']) || isset($data['actu_id']) || isset($data['comment_id']) || isset($data['comment_reply_id']) || isset($data['hmg_id']) || isset($data['hmp_id'])) {

            if( $this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['profil_id']]) ||
                $this->entityManager->getRepository(PostActu::class)->findOneBy(['id' => $data['actu_id']]) || 
                $this->entityManager->getRepository(Comment::class)->findOneBy(['id' => $data['comment_id']]) ||
                $this->entityManager->getRepository(CommentReply::class)->findOneBy(['id' => $data['comment_reply_id']]) || 
                $this->entityManager->getRepository(HistoryMyGame::class)->findOneBy(['id' => $data['hmg_id']]) || 
                $this->entityManager->getRepository(HistoryMyPlateform::class)->findOneBy(['id' => $data['hmp_id']])
                ) {
                    
                
            
                $ip = $request->getClientIp();
                if (!isset($ip)) {
                    $newIp = "0.0.0.0";
                } else {
                    $newIp = $ip;
                }
        
                $warnType = $this->entityManager->getRepository(WarnType::class)->findOneBy(['id' => $data['warn_type_id']]);

                if(!$warnType) {
                    return $this->json(['message' => 'warnType not found']);
                }
        
                $authorizationHeader = $request->headers->get('Authorization');
        
                $user = null;
                if (strpos($authorizationHeader, 'Bearer ') === 0) {
                    $token = substr($authorizationHeader, 7);
                    if(!$token){
                        return $this->json(['message' => 'token is failed']);
                    }
                    $user = $this->entityManager->getRepository(User::class)->findOneBy(['token' => $token]);
        
                    if (!$user) {
                        return $this->json(['message' => 'token is failed']);
                    }
                }


                $warn = new Warn();
                $warn->setWarnType($warnType);
                $warn->setUser($user);
                $warn->setProfil($this->entityManager->getRepository(User::class)->findOneBy(['id' => $data['profil_id']]));
                $warn->setActu($this->entityManager->getRepository(PostActu::class)->findOneBy(['id' => $data['actu_id']]));
                $warn->setComment($this->entityManager->getRepository(Comment::class)->findOneBy(['id' => $data['comment_id']]));
                $warn->setCommentReply($this->entityManager->getRepository(CommentReply::class)->findOneBy(['id' => $data['comment_reply_id']]));
                $warn->setHmg($this->entityManager->getRepository(HistoryMyGame::class)->findOneBy(['id' => $data['hmg_id']]));
                $warn->setHmp($this->entityManager->getRepository(HistoryMyPlateform::class)->findOneBy(['id' => $data['hmp_id']]));
                $warn->setWarnAt(new \DateTimeImmutable());
                $warn->setIp($newIp);
                $warn->setContent($data['content']);

                $this->entityManager->persist($warn);
                $this->entityManager->flush();

                return $this->json(['message' => 'good', 'result' => $warn], 200, [], ['groups' => 'warn:read']);
            }

            return $this->json(['messsage' => 'warn not found']);

        }

        return $this->json(['message' => 'undefine of field']);


    }
}
