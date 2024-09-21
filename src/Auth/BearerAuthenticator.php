<?php

declare(strict_types=1);

namespace App\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class BearerAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtTokenManager,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return !('auth_sign_in' === $request->attributes->get('_route')
            && $request->isMethod('POST'));
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

        $user = $this->userRepository->findOneByEmail($plainData["username"]);
        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Invalid authorization header');
        }

        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
//        $userAgent = $request->headers->get('User-Agent', 'Unknown');
//        $fingerprint = md5($userAgent.$this->salt);
//
//        $refreshToken = $this->refreshTokenRepository->findOneBy([
//            'fingerprint' => $fingerprint,
//        ]);
//
//        if (null !== $refreshToken) {
//            $this->refreshTokenRepository->remove($refreshToken, true);
//        }
//
//        $uuid = Uuid::v7();
//        $accessToken = $this->jwtTokenManager->create($token->getUser());
//        $refreshToken = new RefreshToken();
//        $refreshToken
//            ->setCreatedAt(new \DateTimeImmutable())
//            ->setToken($uuid->jsonSerialize())
//            ->setUserIdentifier($token->getUserIdentifier())
//            ->setFingerprint($fingerprint);
//
//        $this->refreshTokenRepository->save($refreshToken, true);

//        return new JsonResponse([
//            'data' => [
//                'accessToken' => '$accessToken',
//                'refreshToken' => '$uuid->jsonSerialize',
//            ],
//        ]);

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new JsonResponse([
            'message' => 'Auth required',
        ], 401);
    }
}