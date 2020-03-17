<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200317123934 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE uploaded_document DROP CONSTRAINT fk_4a0fa6e7b8b357f6');
        $this->addSql('DROP INDEX uniq_4a0fa6e7b8b357f6');
        $this->addSql('ALTER TABLE uploaded_document DROP proposal_id_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE uploaded_document ADD proposal_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE uploaded_document ADD CONSTRAINT fk_4a0fa6e7b8b357f6 FOREIGN KEY (proposal_id_id) REFERENCES proposal (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_4a0fa6e7b8b357f6 ON uploaded_document (proposal_id_id)');
    }
}
