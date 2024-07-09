<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240709093216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE refresh_tokens DROP created_by');
        $this->addSql('ALTER TABLE refresh_tokens DROP updated_by');
        $this->addSql('ALTER TABLE "user" DROP created_by');
        $this->addSql('ALTER TABLE "user" DROP updated_by');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ADD created_by VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD updated_by VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE refresh_tokens ADD created_by VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE refresh_tokens ADD updated_by VARCHAR(36) NOT NULL');
    }
}
