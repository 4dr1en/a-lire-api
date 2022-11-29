<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221128231526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE comment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE comment (id INT NOT NULL, written_by_id UUID DEFAULT NULL, belong_to_article_id INT NOT NULL, text TEXT NOT NULL, status VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, edited_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, moderated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9474526CAB69C8EF ON comment (written_by_id)');
        $this->addSql('CREATE INDEX IDX_9474526C1AFCB3C6 ON comment (belong_to_article_id)');
        $this->addSql('COMMENT ON COLUMN comment.written_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN comment.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN comment.edited_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN comment.moderated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CAB69C8EF FOREIGN KEY (written_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C1AFCB3C6 FOREIGN KEY (belong_to_article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE comment_id_seq CASCADE');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526CAB69C8EF');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C1AFCB3C6');
        $this->addSql('DROP TABLE comment');
    }
}
