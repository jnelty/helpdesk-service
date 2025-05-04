<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501213058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tags (id SERIAL NOT NULL, name VARCHAR(30) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tag_ticket (tag_id INT NOT NULL, ticket_id INT NOT NULL, PRIMARY KEY(tag_id, ticket_id))');
        $this->addSql('CREATE INDEX IDX_7EE1E48FBAD26311 ON tag_ticket (tag_id)');
        $this->addSql('CREATE INDEX IDX_7EE1E48F700047D2 ON tag_ticket (ticket_id)');
        $this->addSql('CREATE TABLE ticket_statuses (id SERIAL NOT NULL, name VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tickets (id SERIAL NOT NULL, user_id INT DEFAULT NULL, status_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_54469DF4A76ED395 ON tickets (user_id)');
        $this->addSql('CREATE INDEX IDX_54469DF46BF700BD ON tickets (status_id)');
        $this->addSql('COMMENT ON COLUMN tickets.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "users" (id SERIAL NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE tag_ticket ADD CONSTRAINT FK_7EE1E48FBAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_ticket ADD CONSTRAINT FK_7EE1E48F700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4A76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF46BF700BD FOREIGN KEY (status_id) REFERENCES ticket_statuses (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tag_ticket DROP CONSTRAINT FK_7EE1E48FBAD26311');
        $this->addSql('ALTER TABLE tag_ticket DROP CONSTRAINT FK_7EE1E48F700047D2');
        $this->addSql('ALTER TABLE tickets DROP CONSTRAINT FK_54469DF4A76ED395');
        $this->addSql('ALTER TABLE tickets DROP CONSTRAINT FK_54469DF46BF700BD');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE tag_ticket');
        $this->addSql('DROP TABLE ticket_statuses');
        $this->addSql('DROP TABLE tickets');
        $this->addSql('DROP TABLE "users"');
    }
}
