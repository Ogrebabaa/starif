<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240124105930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE materiel_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE metier_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE materiel (id INT NOT NULL, type_id INT NOT NULL, nom VARCHAR(255) DEFAULT NULL, nom_court VARCHAR(255) DEFAULT NULL, marque VARCHAR(255) DEFAULT NULL, prix_public DOUBLE PRECISION DEFAULT NULL, reference_fabricant VARCHAR(255) DEFAULT NULL, commentaire TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_18D2B091C54C8C93 ON materiel (type_id)');
        $this->addSql('CREATE TABLE metier (id INT NOT NULL, nom VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE type (id INT NOT NULL, metier_id INT NOT NULL, nom VARCHAR(255) DEFAULT NULL, famille VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8CDE5729ED16FA20 ON type (metier_id)');
        $this->addSql('ALTER TABLE materiel ADD CONSTRAINT FK_18D2B091C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE type ADD CONSTRAINT FK_8CDE5729ED16FA20 FOREIGN KEY (metier_id) REFERENCES metier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE materiel_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE metier_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE type_id_seq CASCADE');
        $this->addSql('ALTER TABLE materiel DROP CONSTRAINT FK_18D2B091C54C8C93');
        $this->addSql('ALTER TABLE type DROP CONSTRAINT FK_8CDE5729ED16FA20');
        $this->addSql('DROP TABLE materiel');
        $this->addSql('DROP TABLE metier');
        $this->addSql('DROP TABLE type');
    }
}
