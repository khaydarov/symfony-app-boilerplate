<?php

declare(strict_types=1);

namespace App\Auth;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Uid\Uuid;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @throws Exception
     */
    public function findOneByToken(string $token): ?RefreshToken
    {
        $row = $this->connection->fetchAssociative('SELECT * FROM auth WHERE refresh_token = :token', [
            'token' => $token,
        ]);

        if (!$row) {
            return null;
        }

        return new RefreshToken($row['user_id'], $row['token'], $row['expires_at']);
    }

    /**
     * @throws Exception
     */
    public function findOneByUser(User $user): ?RefreshToken
    {
        $row = $this->connection->fetchAssociative('SELECT * FROM auth WHERE user_id = :user_id', [
            'user_id' => $user->getUserIdentifier(),
        ]);

        if (!$row) {
            return null;
        }

        return new RefreshToken($row['token'], $row['user_id']);
    }

    /**
     * @throws Exception
     */
    public function persist(RefreshToken $refreshToken): void
    {
        $this->connection->executeStatement('
            INSERT INTO auth (id, refresh_token, user_id, expires_at)
            VALUES (:id, :refresh_token, :user_id, :expires_at)',
            [
                'id' => $this->generateIdentifier(),
                'refresh_token' => $refreshToken->token,
                'user_id' => $refreshToken->userId,
                'expires_at' => $refreshToken->expiresAt->format('Y-m-d H:i:s'),
            ]);
    }

    public function generateToken(): string
    {
        return Uuid::v4()->toString();
    }

    private function generateIdentifier(): string
    {
        return Uuid::v4()->toString();
    }
}
