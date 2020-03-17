<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200316152234 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE publication ADD consumer_key VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE publication ADD consumer_secret VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE publication ADD access_token VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE publication ADD access_token_secret VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE publication DROP consumer_key');
        $this->addSql('ALTER TABLE publication DROP consumer_secret');
        $this->addSql('ALTER TABLE publication DROP access_token');
        $this->addSql('ALTER TABLE publication DROP access_token_secret');
    }
}
