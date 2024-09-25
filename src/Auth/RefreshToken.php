<?php

declare(strict_types=1);

namespace App\Auth;

readonly class RefreshToken
{
    public function __construct(
        public string $userId,
        public string $token,
        public \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
        public \DateTimeImmutable $expiresAt = new \DateTimeImmutable(),
    ) {
    }
}
