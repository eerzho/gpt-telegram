<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230423174054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE report_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql(
            'CREATE TABLE report (id INT NOT NULL, chat_t_id INT NOT NULL, text TEXT NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_C42F7784FA8569B4 ON report (chat_t_id)');
        $this->addSql(
            'ALTER TABLE report ADD CONSTRAINT FK_C42F7784FA8569B4 FOREIGN KEY (chat_t_id) REFERENCES chat_t (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE report_id_seq CASCADE');
        $this->addSql('ALTER TABLE report DROP CONSTRAINT FK_C42F7784FA8569B4');
        $this->addSql('DROP TABLE report');
    }
}
