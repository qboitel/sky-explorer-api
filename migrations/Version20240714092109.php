<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240714092109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservations DROP CONSTRAINT fk_4da239591cc992');
        $this->addSql('ALTER TABLE courses DROP CONSTRAINT fk_a9a55a4cf53666a8');
        $this->addSql('ALTER TABLE courses DROP CONSTRAINT fk_a9a55a4c5200282e');
        $this->addSql('DROP TABLE courses');
        $this->addSql('ALTER TABLE modules ADD plane_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE modules ADD price DOUBLE PRECISION NOT NULL');
        $this->addSql('COMMENT ON COLUMN modules.plane_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE modules ADD CONSTRAINT FK_2EB743D7F53666A8 FOREIGN KEY (plane_id) REFERENCES planes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2EB743D7F53666A8 ON modules (plane_id)');
        $this->addSql('DROP INDEX idx_4da239591cc992');
        $this->addSql('ALTER TABLE reservations RENAME COLUMN course_id TO module_id');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239AFC2B591 FOREIGN KEY (module_id) REFERENCES modules (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4DA239AFC2B591 ON reservations (module_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE courses (id UUID NOT NULL, plane_id UUID DEFAULT NULL, formation_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, price DOUBLE PRECISION NOT NULL, subjects TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_a9a55a4c5200282e ON courses (formation_id)');
        $this->addSql('CREATE INDEX idx_a9a55a4cf53666a8 ON courses (plane_id)');
        $this->addSql('COMMENT ON COLUMN courses.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN courses.plane_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN courses.formation_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN courses.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN courses.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT fk_a9a55a4cf53666a8 FOREIGN KEY (plane_id) REFERENCES planes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT fk_a9a55a4c5200282e FOREIGN KEY (formation_id) REFERENCES formations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservations DROP CONSTRAINT FK_4DA239AFC2B591');
        $this->addSql('DROP INDEX IDX_4DA239AFC2B591');
        $this->addSql('ALTER TABLE reservations RENAME COLUMN module_id TO course_id');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT fk_4da239591cc992 FOREIGN KEY (course_id) REFERENCES courses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_4da239591cc992 ON reservations (course_id)');
        $this->addSql('ALTER TABLE modules DROP CONSTRAINT FK_2EB743D7F53666A8');
        $this->addSql('DROP INDEX IDX_2EB743D7F53666A8');
        $this->addSql('ALTER TABLE modules DROP plane_id');
        $this->addSql('ALTER TABLE modules DROP price');
    }
}
