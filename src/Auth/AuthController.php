<?php

declare(strict_types=1);

namespace App\Auth;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly RefreshTokenRepositoryInterface $refreshTokenRepository,
        private readonly JWTTokenManagerInterface $jwtTokenManager,
    ) {
    }

    #[Route('/sign-in', name: 'auth_sign_in', methods: ['POST'])]
    public function signIn(Request $request): Response
    {
        $body = json_decode($request->getContent(), true);
        if (!$body) {
            return new JsonResponse([
                'message' => 'Invalid JSON'
            ], Response::HTTP_BAD_REQUEST);
        }

        $email = $body['email'];
        $password = $body['password'];

        $user = $this->userRepository->findOneByEmail($email);
        if (!$user) {
            return new JsonResponse([
                'message' => 'User not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if (!$this->userPasswordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse([
                'message' => 'Invalid password'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $refreshToken = $this->refreshTokenRepository->findOneByUser($user);
        $jwtToken = $this->jwtTokenManager->create($user);

        return new JsonResponse([
            'accessToken' => $jwtToken,
            'refreshToken' => $refreshToken->token
        ]);
    }

    public function signUp(Request $request): Response
    {
        $body = json_decode($request->getContent(), true);
        if (!$body) {
            return new JsonResponse([
                'message' => 'Invalid JSON'
            ], Response::HTTP_BAD_REQUEST);
        }

        $email = $body['email'];
        $password = $body['password'];

        $user = $this->userRepository->findOneByEmail($email);
        if ($user) {
            return new JsonResponse([
                'message' => 'User already exists'
            ], Response::HTTP_CONFLICT);
        }

//        $user->

        return new JsonResponse([
            'message' => 'User created'
        ]);
    }
}