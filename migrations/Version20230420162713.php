<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230420162713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE chat_t_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE command_t_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE message_t_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE chat_t (id INT NOT NULL, telegram_id INT NOT NULL, chat_gpt_api_token VARCHAR(255) DEFAULT NULL, chat_gpt_model VARCHAR(255) DEFAULT \'gpt-3.5-turbo\' NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql(
            'CREATE TABLE command_t (id INT NOT NULL, chat_t_id INT NOT NULL, active BOOLEAN DEFAULT false NOT NULL, class VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8E1963F6FA8569B4 ON command_t (chat_t_id)');
        $this->addSql(
            'CREATE TABLE message_t (id INT NOT NULL, chat_t_id INT DEFAULT NULL, role VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_A5EC0569FA8569B4 ON message_t (chat_t_id)');
        $this->addSql(
            'ALTER TABLE command_t ADD CONSTRAINT FK_8E1963F6FA8569B4 FOREIGN KEY (chat_t_id) REFERENCES chat_t (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE message_t ADD CONSTRAINT FK_A5EC0569FA8569B4 FOREIGN KEY (chat_t_id) REFERENCES chat_t (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE chat_t_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE command_t_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE message_t_id_seq CASCADE');
        $this->addSql('ALTER TABLE command_t DROP CONSTRAINT FK_8E1963F6FA8569B4');
        $this->addSql('ALTER TABLE message_t DROP CONSTRAINT FK_A5EC0569FA8569B4');
        $this->addSql('DROP TABLE chat_t');
        $this->addSql('DROP TABLE command_t');
        $this->addSql('DROP TABLE message_t');
    }
}
