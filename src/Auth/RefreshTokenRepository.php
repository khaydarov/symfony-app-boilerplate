<?php

declare(strict_types=1);

namespace App\Auth;

use Doctrine\DBAL\Connection;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function findOneByToken(string $token): ?RefreshToken
    {
        $row = $this->connection->fetchAssociative('SELECT * FROM refresh_tokens WHERE token = :token', [
            'token' => $token,
        ]);

        if (!$row) {
            return null;
        }

        return new RefreshToken($row['token'], $row['username']);
    }

    public function findOneByUser(User $user): ?RefreshToken
    {
        $row = $this->connection->fetchAssociative('SELECT * FROM refresh_tokens WHERE user_id = :user_id', [
            'user_id' => $user->getUserIdentifier(),
        ]);

        if (!$row) {
            return null;
        }

        return new RefreshToken($row['token'], $row['user_id']);
    }

    public function persist(RefreshToken $refreshToken): void
    {
        $this->connection->executeStatement('INSERT INTO refresh_tokens (token, user_id, expires_at) VALUES (:token, :username, :expires_at)', [
            'token' => $refreshToken->token,
            'email' => $refreshToken->email,
        ]);
    }
}
