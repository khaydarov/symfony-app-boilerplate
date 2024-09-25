<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240925065739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'adds auth module tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS Users (
                id UUID PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT NOW(),
                updated_at TIMESTAMP NOT NULL DEFAULT NOW()
            );
        ');

        $this->addSql('
            CREATE TABLE IF NOT EXISTS Auth (
                id UUID PRIMARY KEY,
                user_id UUID NOT NULL,
                refresh_token VARCHAR(255) NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT NOW(),
                expires_at TIMESTAMP NOT NULL DEFAULT NOW()
            );
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE Users');
        $this->addSql('DROP TABLE Auth');
    }
}
