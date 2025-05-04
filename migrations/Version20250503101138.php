<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250503101138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket_message_types (id SERIAL NOT NULL, name VARCHAR(32) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE ticket_messages (id SERIAL NOT NULL, type_id INT NOT NULL, user_id INT DEFAULT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5E6BE217C54C8C93 ON ticket_messages (type_id)');
        $this->addSql('CREATE INDEX IDX_5E6BE217A76ED395 ON ticket_messages (user_id)');
        $this->addSql('ALTER TABLE ticket_messages ADD CONSTRAINT FK_5E6BE217C54C8C93 FOREIGN KEY (type_id) REFERENCES ticket_message_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_messages ADD CONSTRAINT FK_5E6BE217A76ED395 FOREIGN KEY (user_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ticket_messages DROP CONSTRAINT FK_5E6BE217C54C8C93');
        $this->addSql('ALTER TABLE ticket_messages DROP CONSTRAINT FK_5E6BE217A76ED395');
        $this->addSql('DROP TABLE ticket_message_types');
        $this->addSql('DROP TABLE ticket_messages');
    }
}
