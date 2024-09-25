<?php

declare(strict_types=1);

namespace App\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/{version}/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly RefreshTokenRepositoryInterface $refreshTokenRepository,
        private readonly JWTTokenManagerInterface $jwtTokenManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/me', name: 'auth_me', methods: ['GET'])]
    public function me(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return new JsonResponse([
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route('/sign-in', name: 'auth_sign_in', methods: ['POST'])]
    public function signIn(Request $request): Response
    {
        $body = json_decode($request->getContent(), true);
        if (!$body) {
            return new JsonResponse([
                'message' => 'Invalid JSON',
            ], Response::HTTP_BAD_REQUEST);
        }

        $email = $body['email'];
        $password = $body['password'];

        $user = $this->userRepository->findOneByEmail($email);
        if (!$user) {
            return new JsonResponse([
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        if (!$this->userPasswordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse([
                'message' => 'Invalid password',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $this->logger->info('generating refresh token with expiration date of 1 month');
        $refreshToken = new RefreshToken(
            $user->getUserIdentifier(),
            $this->refreshTokenRepository->generateToken(),
            new \DateTimeImmutable(),
            new \DateTimeImmutable('+1 month'),
        );

        $this->refreshTokenRepository->persist($refreshToken);
        $jwtToken = $this->jwtTokenManager->createFromPayload($user, [
            'userId' => $user->getUserIdentifier(),
        ]);

        return new JsonResponse([
            'accessToken' => $jwtToken,
            'refreshToken' => $refreshToken->token,
        ]);
    }

    #[Route('/sign-up', name: 'auth_sign_up', methods: ['POST'])]
    public function signUp(Request $request): Response
    {
        $body = json_decode($request->getContent(), true);
        if (!$body) {
            return new JsonResponse([
                'message' => 'Invalid JSON',
            ], Response::HTTP_BAD_REQUEST);
        }

        $email = $body['email'];
        $password = $body['password'];

        $user = $this->userRepository->findOneByEmail($email);
        if ($user) {
            return new JsonResponse([
                'message' => 'User already exists',
            ], Response::HTTP_CONFLICT);
        }

        $this->logger->info('generating hash for user password');
        $password = $this->userPasswordHasher->hashPassword(new User('', $email, ''), $password);
        $user = new User(
            $this->userRepository->generateIdentifier(),
            $email,
            $password
        );

        $this->logger->info('persisting user');
        $this->userRepository->persist($user);

        return new JsonResponse(data: [
            'message' => 'User successfully created',
            'data' => [
                'id' => $user->getUserIdentifier(),
            ],
        ]);
    }
}
