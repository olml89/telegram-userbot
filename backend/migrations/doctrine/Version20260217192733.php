<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260217192733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add audio files specialization table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE audio_files (
                id INT NOT NULL, 
                duration NUMERIC(10, 2) NOT NULL, 
                PRIMARY KEY (id)
            )
        ');
        $this->addSql('
            ALTER TABLE audio_files
            ADD CONSTRAINT FK_187D3695BF396750 
            FOREIGN KEY (id) 
            REFERENCES files (id) 
            ON DELETE CASCADE
        ');
        $this->addSql('
            ALTER TABLE files 
            ADD type VARCHAR(255) NOT NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE audio_files
            DROP CONSTRAINT FK_187D3695BF396750
        ');
        $this->addSql('
            DROP TABLE audio_files
        ');
        $this->addSql('
            ALTER TABLE files DROP type
        ');
    }
}
