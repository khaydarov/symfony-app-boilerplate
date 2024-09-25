<?php

declare(strict_types=1);

namespace App\Auth;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $password,
        public readonly \DateTimeImmutable $createdAt = new \DateTimeImmutable(),
        public readonly \DateTimeImmutable $updatedAt = new \DateTimeImmutable(),
    ) {
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return [];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->id;
    }
}
