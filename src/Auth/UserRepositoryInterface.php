<?php

declare(strict_types=1);

namespace App\Auth;

interface UserRepositoryInterface
{
    public function findOneById(string $id): ?User;

    public function findOneByEmail(string $email): ?User;

    public function persist(User $user): void;

    public function generateIdentifier(): string;
}
