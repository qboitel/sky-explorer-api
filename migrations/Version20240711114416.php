<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240711114416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE formation_course DROP CONSTRAINT fk_368761945200282e');
        $this->addSql('ALTER TABLE formation_course DROP CONSTRAINT fk_36876194591cc992');
        $this->addSql('DROP TABLE formation_course');
        $this->addSql('ALTER TABLE courses ADD formation_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN courses.formation_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4C5200282E FOREIGN KEY (formation_id) REFERENCES formations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A9A55A4C5200282E ON courses (formation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE formation_course (formation_id UUID NOT NULL, course_id UUID NOT NULL, PRIMARY KEY(formation_id, course_id))');
        $this->addSql('CREATE INDEX idx_36876194591cc992 ON formation_course (course_id)');
        $this->addSql('CREATE INDEX idx_368761945200282e ON formation_course (formation_id)');
        $this->addSql('COMMENT ON COLUMN formation_course.formation_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN formation_course.course_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE formation_course ADD CONSTRAINT fk_368761945200282e FOREIGN KEY (formation_id) REFERENCES formations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation_course ADD CONSTRAINT fk_36876194591cc992 FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE courses DROP CONSTRAINT FK_A9A55A4C5200282E');
        $this->addSql('DROP INDEX IDX_A9A55A4C5200282E');
        $this->addSql('ALTER TABLE courses DROP formation_id');
    }
}
