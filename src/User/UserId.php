<?php

declare(strict_types=1);

namespace App\User;

class UserId
{
    public function __construct(
        public readonly string $value,
    ) {
    }
}
