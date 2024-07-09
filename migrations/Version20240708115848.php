<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240708115848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE competences (id UUID NOT NULL, module_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DB2077CEAFC2B591 ON competences (module_id)');
        $this->addSql('COMMENT ON COLUMN competences.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN competences.module_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN competences.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN competences.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE courses (id UUID NOT NULL, plane_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, price DOUBLE PRECISION NOT NULL, subjects TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A9A55A4CF53666A8 ON courses (plane_id)');
        $this->addSql('COMMENT ON COLUMN courses.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN courses.plane_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN courses.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN courses.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE facture_items (id UUID NOT NULL, facture_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, quantity INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_732A61FA7F2DEE08 ON facture_items (facture_id)');
        $this->addSql('COMMENT ON COLUMN facture_items.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN facture_items.facture_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN facture_items.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN facture_items.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE factures (id UUID NOT NULL, user_id UUID DEFAULT NULL, total DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_647590BA76ED395 ON factures (user_id)');
        $this->addSql('COMMENT ON COLUMN factures.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN factures.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN factures.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN factures.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE formations (id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, price DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN formations.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN formations.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN formations.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE formation_course (formation_id UUID NOT NULL, course_id UUID NOT NULL, PRIMARY KEY(formation_id, course_id))');
        $this->addSql('CREATE INDEX IDX_368761945200282E ON formation_course (formation_id)');
        $this->addSql('CREATE INDEX IDX_36876194591CC992 ON formation_course (course_id)');
        $this->addSql('COMMENT ON COLUMN formation_course.formation_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN formation_course.course_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE licenses (id UUID NOT NULL, user_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, file VARCHAR(255) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, obtained_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7F320F3FA76ED395 ON licenses (user_id)');
        $this->addSql('COMMENT ON COLUMN licenses.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN licenses.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN licenses.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN licenses.obtained_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN licenses.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN licenses.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE medical_certificates (id UUID NOT NULL, user_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, file VARCHAR(255) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, obtained_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A8DBD030A76ED395 ON medical_certificates (user_id)');
        $this->addSql('COMMENT ON COLUMN medical_certificates.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN medical_certificates.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN medical_certificates.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN medical_certificates.obtained_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN medical_certificates.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN medical_certificates.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE modules (id UUID NOT NULL, formation_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2EB743D75200282E ON modules (formation_id)');
        $this->addSql('COMMENT ON COLUMN modules.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN modules.formation_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN modules.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN modules.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE planes (id UUID NOT NULL, immatriculation VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN planes.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN planes.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN planes.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE reservations (id UUID NOT NULL, user_id UUID DEFAULT NULL, instructor_id UUID DEFAULT NULL, course_id UUID DEFAULT NULL, starts_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, ends_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, feedbacks TEXT NOT NULL, follow_up TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4DA239A76ED395 ON reservations (user_id)');
        $this->addSql('CREATE INDEX IDX_4DA2398C4FC193 ON reservations (instructor_id)');
        $this->addSql('CREATE INDEX IDX_4DA239591CC992 ON reservations (course_id)');
        $this->addSql('COMMENT ON COLUMN reservations.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reservations.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reservations.instructor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reservations.course_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reservations.starts_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reservations.ends_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reservations.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reservations.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, last_name VARCHAR(100) NOT NULL, first_name VARCHAR(100) NOT NULL, phone VARCHAR(10) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE competences ADD CONSTRAINT FK_DB2077CEAFC2B591 FOREIGN KEY (module_id) REFERENCES modules (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4CF53666A8 FOREIGN KEY (plane_id) REFERENCES planes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE facture_items ADD CONSTRAINT FK_732A61FA7F2DEE08 FOREIGN KEY (facture_id) REFERENCES factures (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE factures ADD CONSTRAINT FK_647590BA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation_course ADD CONSTRAINT FK_368761945200282E FOREIGN KEY (formation_id) REFERENCES formations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE formation_course ADD CONSTRAINT FK_36876194591CC992 FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE licenses ADD CONSTRAINT FK_7F320F3FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE medical_certificates ADD CONSTRAINT FK_A8DBD030A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE modules ADD CONSTRAINT FK_2EB743D75200282E FOREIGN KEY (formation_id) REFERENCES formations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA2398C4FC193 FOREIGN KEY (instructor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA239591CC992 FOREIGN KEY (course_id) REFERENCES courses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE competences DROP CONSTRAINT FK_DB2077CEAFC2B591');
        $this->addSql('ALTER TABLE courses DROP CONSTRAINT FK_A9A55A4CF53666A8');
        $this->addSql('ALTER TABLE facture_items DROP CONSTRAINT FK_732A61FA7F2DEE08');
        $this->addSql('ALTER TABLE factures DROP CONSTRAINT FK_647590BA76ED395');
        $this->addSql('ALTER TABLE formation_course DROP CONSTRAINT FK_368761945200282E');
        $this->addSql('ALTER TABLE formation_course DROP CONSTRAINT FK_36876194591CC992');
        $this->addSql('ALTER TABLE licenses DROP CONSTRAINT FK_7F320F3FA76ED395');
        $this->addSql('ALTER TABLE medical_certificates DROP CONSTRAINT FK_A8DBD030A76ED395');
        $this->addSql('ALTER TABLE modules DROP CONSTRAINT FK_2EB743D75200282E');
        $this->addSql('ALTER TABLE reservations DROP CONSTRAINT FK_4DA239A76ED395');
        $this->addSql('ALTER TABLE reservations DROP CONSTRAINT FK_4DA2398C4FC193');
        $this->addSql('ALTER TABLE reservations DROP CONSTRAINT FK_4DA239591CC992');
        $this->addSql('DROP TABLE competences');
        $this->addSql('DROP TABLE courses');
        $this->addSql('DROP TABLE facture_items');
        $this->addSql('DROP TABLE factures');
        $this->addSql('DROP TABLE formations');
        $this->addSql('DROP TABLE formation_course');
        $this->addSql('DROP TABLE licenses');
        $this->addSql('DROP TABLE medical_certificates');
        $this->addSql('DROP TABLE modules');
        $this->addSql('DROP TABLE planes');
        $this->addSql('DROP TABLE reservations');
        $this->addSql('DROP TABLE "user"');
    }
}
