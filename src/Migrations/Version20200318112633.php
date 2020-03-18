<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200318112633 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE social_network_uploaded_document (social_network_id INT NOT NULL, uploaded_document_id INT NOT NULL, PRIMARY KEY(social_network_id, uploaded_document_id))');
        $this->addSql('CREATE INDEX IDX_FFD53D8AFA413953 ON social_network_uploaded_document (social_network_id)');
        $this->addSql('CREATE INDEX IDX_FFD53D8AA20E05A1 ON social_network_uploaded_document (uploaded_document_id)');
        $this->addSql('ALTER TABLE social_network_uploaded_document ADD CONSTRAINT FK_FFD53D8AFA413953 FOREIGN KEY (social_network_id) REFERENCES social_network (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE social_network_uploaded_document ADD CONSTRAINT FK_FFD53D8AA20E05A1 FOREIGN KEY (uploaded_document_id) REFERENCES uploaded_document (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE social_network_uploaded_document');
    }
}
