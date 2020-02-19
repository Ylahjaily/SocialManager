<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200217203038 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE comment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE like_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE proposal_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE review_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE review_comment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE social_network_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE uploaded_document_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE comment (id INT NOT NULL, proposal_id_id INT NOT NULL, user_id_id INT NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9474526CB8B357F6 ON comment (proposal_id_id)');
        $this->addSql('CREATE INDEX IDX_9474526C9D86650F ON comment (user_id_id)');
        $this->addSql('CREATE TABLE "like" (id INT NOT NULL, proposal_id_id INT NOT NULL, user_id_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AC6340B3B8B357F6 ON "like" (proposal_id_id)');
        $this->addSql('CREATE INDEX IDX_AC6340B39D86650F ON "like" (user_id_id)');
        $this->addSql('CREATE TABLE proposal (id INT NOT NULL, user_id_id INT NOT NULL, title VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, text_content TEXT NOT NULL, link VARCHAR(255) NOT NULL, is_published BOOLEAN NOT NULL, date_publication_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BFE594729D86650F ON proposal (user_id_id)');
        $this->addSql('CREATE TABLE review (id INT NOT NULL, proposal_id_id INT NOT NULL, user_id_id INT NOT NULL, is_approved BOOLEAN NOT NULL, decision_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_794381C6B8B357F6 ON review (proposal_id_id)');
        $this->addSql('CREATE INDEX IDX_794381C69D86650F ON review (user_id_id)');
        $this->addSql('CREATE TABLE review_comment (id INT NOT NULL, review_id_id INT NOT NULL, comments TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F9AE69B6CCAB24C ON review_comment (review_id_id)');
        $this->addSql('CREATE TABLE social_network (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE social_network_user (social_network_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(social_network_id, user_id))');
        $this->addSql('CREATE INDEX IDX_2A6DBC01FA413953 ON social_network_user (social_network_id)');
        $this->addSql('CREATE INDEX IDX_2A6DBC01A76ED395 ON social_network_user (user_id)');
        $this->addSql('CREATE TABLE social_network_proposal (social_network_id INT NOT NULL, proposal_id INT NOT NULL, PRIMARY KEY(social_network_id, proposal_id))');
        $this->addSql('CREATE INDEX IDX_6688502EFA413953 ON social_network_proposal (social_network_id)');
        $this->addSql('CREATE INDEX IDX_6688502EF4792058 ON social_network_proposal (proposal_id)');
        $this->addSql('CREATE TABLE uploaded_document (id INT NOT NULL, proposal_id_id INT NOT NULL, title VARCHAR(255) NOT NULL, data BYTEA NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4A0FA6E7B8B357F6 ON uploaded_document (proposal_id_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, roles TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, api_key VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "user".roles IS \'(DC2Type:simple_array)\'');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB8B357F6 FOREIGN KEY (proposal_id_id) REFERENCES proposal (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C9D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "like" ADD CONSTRAINT FK_AC6340B3B8B357F6 FOREIGN KEY (proposal_id_id) REFERENCES proposal (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "like" ADD CONSTRAINT FK_AC6340B39D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE proposal ADD CONSTRAINT FK_BFE594729D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6B8B357F6 FOREIGN KEY (proposal_id_id) REFERENCES proposal (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C69D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review_comment ADD CONSTRAINT FK_F9AE69B6CCAB24C FOREIGN KEY (review_id_id) REFERENCES review (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE social_network_user ADD CONSTRAINT FK_2A6DBC01FA413953 FOREIGN KEY (social_network_id) REFERENCES social_network (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE social_network_user ADD CONSTRAINT FK_2A6DBC01A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE social_network_proposal ADD CONSTRAINT FK_6688502EFA413953 FOREIGN KEY (social_network_id) REFERENCES social_network (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE social_network_proposal ADD CONSTRAINT FK_6688502EF4792058 FOREIGN KEY (proposal_id) REFERENCES proposal (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE uploaded_document ADD CONSTRAINT FK_4A0FA6E7B8B357F6 FOREIGN KEY (proposal_id_id) REFERENCES proposal (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CB8B357F6');
        $this->addSql('ALTER TABLE "like" DROP CONSTRAINT FK_AC6340B3B8B357F6');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C6B8B357F6');
        $this->addSql('ALTER TABLE social_network_proposal DROP CONSTRAINT FK_6688502EF4792058');
        $this->addSql('ALTER TABLE uploaded_document DROP CONSTRAINT FK_4A0FA6E7B8B357F6');
        $this->addSql('ALTER TABLE review_comment DROP CONSTRAINT FK_F9AE69B6CCAB24C');
        $this->addSql('ALTER TABLE social_network_user DROP CONSTRAINT FK_2A6DBC01FA413953');
        $this->addSql('ALTER TABLE social_network_proposal DROP CONSTRAINT FK_6688502EFA413953');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C9D86650F');
        $this->addSql('ALTER TABLE "like" DROP CONSTRAINT FK_AC6340B39D86650F');
        $this->addSql('ALTER TABLE proposal DROP CONSTRAINT FK_BFE594729D86650F');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C69D86650F');
        $this->addSql('ALTER TABLE social_network_user DROP CONSTRAINT FK_2A6DBC01A76ED395');
        $this->addSql('DROP SEQUENCE comment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE like_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE proposal_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE review_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE review_comment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE social_network_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE uploaded_document_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE "like"');
        $this->addSql('DROP TABLE proposal');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE review_comment');
        $this->addSql('DROP TABLE social_network');
        $this->addSql('DROP TABLE social_network_user');
        $this->addSql('DROP TABLE social_network_proposal');
        $this->addSql('DROP TABLE uploaded_document');
        $this->addSql('DROP TABLE "user"');
    }
}
