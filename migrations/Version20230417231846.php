<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230417231846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE chat_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE command_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE chat (id INT NOT NULL, telegram_id INT NOT NULL, chat_gpt_api_token VARCHAR(255) DEFAULT NULL, chat_gpt_model VARCHAR(255) DEFAULT \'text-davinci-003\' NOT NULL, temperature INT DEFAULT 1 NOT NULL, max_tokens INT DEFAULT 1000 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE command (id INT NOT NULL, chat_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, active BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8ECAEAD41A9A7125 ON command (chat_id)');
        $this->addSql('ALTER TABLE command ADD CONSTRAINT FK_8ECAEAD41A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE chat_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE command_id_seq CASCADE');
        $this->addSql('ALTER TABLE command DROP CONSTRAINT FK_8ECAEAD41A9A7125');
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE command');
    }
}
