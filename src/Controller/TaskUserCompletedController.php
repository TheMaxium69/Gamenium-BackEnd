<?php

namespace App\Controller;

use App\Entity\TaskUserCompleted;
use App\Repository\TaskUserCompletedRepository;
use App\Repository\TaskUserRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('task-user-completed')]
class TaskUserCompletedController extends AbstractController
{
    private TaskUserCompletedRepository $taskUserCompletedRepository;
    private TaskUserRepository $taskUserRepository;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        TaskUserCompletedRepository $taskUserCompletedRepository,
        TaskUserRepository $taskUserRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->taskUserCompletedRepository = $taskUserCompletedRepository;
        $this->taskUserRepository = $taskUserRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/view', name: 'view_completed_tasks', methods: ['GET'])]
    public function viewCompletedTasks(Request $request): JsonResponse
    {
        // extraction du bearer dans les entete d'authorization
        $authorizationHeader = $request->headers->get('Authorization');
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            return $this->json(['message' => 'Aucun token fourni']);
        }

        // extraction du token
        $token = substr($authorizationHeader, 7);

        // Trouve l'user via le token
        $user = $this->userRepository->findOneBy(['token' => $token]);
        if (!$user) {
            return $this->json(['message' => 'Token invalide ou utilisateur introuvable']);
        }

        // Récupère les taches complete par user
        $completedTasks = $this->taskUserCompletedRepository->createQueryBuilder('tuc')
        ->select('
            tuc.id AS completed_id,
            tuc.completed_at AS completed_at,
            tu.id AS task_id,
            tu.name AS task_name,
            u.id AS user_id,
            u.username AS username
        ')
        ->join('tuc.taskuser', 'tu') // Join TaskUser
        ->join('tuc.user', 'u') // Join User
        ->where('tuc.user = :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getArrayResult();



        if (!$completedTasks) {
            return $this->json(['message' => 'Aucune tâche complétée trouvée', 'result' => []]); 
        }


        return $this->json(['message' => 'good', 'result' => $completedTasks], 200, [], ['groups' => 'taskusercompleted:read']);
    }


    #[Route('/complete-task', name: 'complete_task', methods: ['POST'])]
    public function completeTask(Request $request): JsonResponse
    {

        // Récup l'ID de la tache a completer depuis le corps de la requete
        $data = json_decode($request->getContent(),true);
        $taskId = $data['taskId'] ?? null;

        if (!$taskId) {
            return $this->json(['message' => 'ID de la tâche requis']);
        }

        // Récupérer le token Bearer depuis l'entête d'auhtorization
        $authorizationHeader = $request->headers->get('Authorization');
        if(!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            return $this->json(['message' => 'Aucun token Fourni']);
        }

        // Extraire le token après "Bearer "
        $token = substr($authorizationHeader, 7);

        // Récuperer l'utilisateur lié au token
        $user = $this->userRepository->findOneBy(['token' => $token]);
        if (!$user) {
            return $this->json(['message' => 'Token invalide ou utilisateur introuvable']);
        }

        // Vérifier si la tache existe
        $task = $this->taskUserRepository->find($taskId);
        if (!$task){
            return $this->json(['message' => 'Tâche non trouvée']);
        } 

        /* SET UNE IP */
        if (!isset($data['ip'])) {
            $newIp = "0.0.0.0";
        } else {
            $newIp = $data['ip'];
        }

        // Verifier si la tache est déjà complétée par cet utilisateur
        $existingCompletion = $this->taskUserCompletedRepository->findOneBy(['user' => $user, 'taskuser' => $task]);

        if (!$existingCompletion) {
            // Si elle n'est pas complété on crée une entré
            $completedTask = new TaskUserCompleted();
            $completedTask->setUser($user);
            $completedTask->setTaskuser($task);
            $completedTask->setIp($newIp);
            $completedTask->setCompletedAt(new \DateTimeImmutable());


            // persist et enregistrement de la nouvelle entrée
            $this->entityManager->persist($completedTask);
            $this->entityManager->flush();

            return $this->json(['message'=> 'Tâche complétée']);    
        }

        return $this->json(['message' => 'Tâche déjà complétée par cet utilisateur']);
    }

}

