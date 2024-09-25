<?php

declare(strict_types=1);

namespace App\Auth;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

readonly class UserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneById($identifier);
        if (!$user) {
            throw new \Exception('User not found');
        }

        return $user;
    }
}
