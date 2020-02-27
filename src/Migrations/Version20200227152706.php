<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200227152706 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE publication_id_seq CASCADE');
        $this->addSql('DROP TABLE publication');
        $this->addSql('ALTER TABLE proposal ADD published_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE594725B075477 FOREIGN KEY (published_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BFE594725B075477 ON proposal (published_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE publication_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE publication (id INT NOT NULL, user_id_id INT NOT NULL, proposal_id_id INT NOT NULL, social_network_id_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_af3c6779b8b357f6 ON publication (proposal_id_id)');
        $this->addSql('CREATE INDEX idx_af3c67799d86650f ON publication (user_id_id)');
        $this->addSql('CREATE INDEX idx_af3c67797e4a99f ON publication (social_network_id_id)');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT fk_af3c67799d86650f FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT fk_af3c6779b8b357f6 FOREIGN KEY (proposal_id_id) REFERENCES proposal (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT fk_af3c67797e4a99f FOREIGN KEY (social_network_id_id) REFERENCES social_network (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE proposal DROP CONSTRAINT FK_BFE594725B075477');
        $this->addSql('DROP INDEX IDX_BFE594725B075477');
        $this->addSql('ALTER TABLE proposal DROP published_by_id');
    }
}
