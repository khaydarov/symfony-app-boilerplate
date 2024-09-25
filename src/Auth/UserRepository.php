<?php

declare(strict_types=1);

namespace App\Auth;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Component\Uid\Uuid;

readonly class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function findOneById(string $id): ?User
    {
        $row = $this->connection->fetchAssociative('SELECT * FROM users WHERE id = :id', [
            'id' => $id,
        ]);

        if (!$row) {
            return null;
        }

        return $this->createUserFromRaw($row);
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

        return $this->createUserFromRaw($row);
    }

    public function persist(User $user): void
    {
        $this->connection->executeQuery('INSERT INTO users (id, email, password) VALUES (:id, :email, :password)', [
            'id' => $user->getUserIdentifier(),
            'email' => $user->email,
            'password' => $user->password,
        ]);
    }

    public function generateIdentifier(): string
    {
        return Uuid::v4()->toString();
    }

    private function createUserFromRaw(array $raw): User
    {
        return new User(
            $raw['id'],
            $raw['email'],
            $raw['password'],
            new \DateTimeImmutable($raw['created_at']),
            new \DateTimeImmutable($raw['updated_at'])
        );
    }
}
