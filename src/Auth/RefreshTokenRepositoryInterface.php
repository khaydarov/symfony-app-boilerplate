<?php

declare(strict_types=1);

namespace App\Auth;

interface RefreshTokenRepositoryInterface
{
    public function findOneByToken(string $token): ?RefreshToken;

    public function findOneByUser(User $user): ?RefreshToken;

    public function persist(RefreshToken $refreshToken): void;
}