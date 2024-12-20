<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class AccessTokenAuthenticator extends AbstractAuthenticator
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request): ?bool
    {
        // Vérifiez si une requête contient un token (par exemple dans l'en-tête Authorization)
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $tokenBearer = $request->headers->get('Authorization');
        $token = substr($tokenBearer, 7);

        if (!$token) {
            throw new AuthenticationException('Aucun token trouvé dans la requête.');
        }

        // Retournez un PassPort (nouvelle approche Symfony) pour valider l'identité
        return new SelfValidatingPassport(
            new UserBadge($token, function ($token) {
                return $this->userRepository->findOneBy(['token' => $token]);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Authentification réussie, continuez le traitement normalement.
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Ce qui se passe lorsqu’une authentification échoue
        return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_UNAUTHORIZED);
    }
}