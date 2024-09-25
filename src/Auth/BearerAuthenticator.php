<?php

declare(strict_types=1);

namespace App\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class BearerAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private readonly array $skipRoutes;

    public function __construct(
        private readonly JWTTokenManagerInterface $jwtTokenManager,
        private readonly UserRepositoryInterface $userRepository,
    ) {
        $this->skipRoutes = [
            'auth_sign_in',
            'auth_sign_up',
            'auth_access_token',
        ];
    }

    public function supports(Request $request): ?bool
    {
        if (in_array($request->attributes->get('_route'), $this->skipRoutes)) {
            return false;
        }

        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $authorizationHeader = $request->headers->get('Authorization');
        if (null === $authorizationHeader) {
            throw new CustomUserMessageAuthenticationException('Auth required');
        }

        if (1 !== preg_match('/^Bearer\s+(.+)$/', $authorizationHeader, $matches)) {
            throw new CustomUserMessageAuthenticationException('Invalid authorization header');
        }

        $accessToken = $matches[1];
        $plainData = $this->jwtTokenManager->parse($accessToken);
        $user = $this->userRepository->findOneById($plainData['userId']);
        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Invalid authorization header');
        }

        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new JsonResponse([
            'message' => 'Auth required',
        ], 401);
    }
}
