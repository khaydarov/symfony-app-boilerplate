<?php

declare(strict_types=1);

namespace App\Auth;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

readonly class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @throws Exception
     */
    public function findOneByEmail(string $email): ?User
    {
        $row = $this->connection->fetchAssociative('SELECT * FROM users WHERE email = :email', [
            'email' => $email,
        ]);

        if (!$row) {
            return null;
        }

        return new User($row['email'], $row['password']);
    }

    public function persist(User $user): void
    {
        // TODO: Implement persist() method.
    }
}
