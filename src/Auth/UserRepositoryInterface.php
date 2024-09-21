<?php

declare(strict_types=1);

namespace App\Auth;

interface UserRepositoryInterface
{
    /**
     * Find a user by email
     */
    public function findOneByEmail(string $email): ?User;

    /**
     * Persist a user
     */
    public function persist(User $user): void;
}