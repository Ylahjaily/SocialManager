<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200318111753 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE comment ADD uploaded_document_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C24342B87 FOREIGN KEY (uploaded_document_id_id) REFERENCES uploaded_document (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9474526C24342B87 ON comment (uploaded_document_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C24342B87');
        $this->addSql('DROP INDEX IDX_9474526C24342B87');
        $this->addSql('ALTER TABLE comment DROP uploaded_document_id_id');
    }
}
