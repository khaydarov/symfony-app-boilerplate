<?php

declare(strict_types=1);

namespace App\Auth;

readonly class RefreshToken
{
    public function __construct(
        public string             $token,
        public string             $email,
    ) {
    }
}