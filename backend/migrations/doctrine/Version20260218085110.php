<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260218085110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add video files specialization table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE video_files (
                id INT NOT NULL, 
                duration NUMERIC(10, 2) NOT NULL, 
                width INT NOT NULL, 
                height INT NOT NULL, 
                PRIMARY KEY (id)
            )
        ');
        $this->addSql('
            ALTER TABLE video_files 
            ADD CONSTRAINT FK_895749FBBF396750 
            FOREIGN KEY (id) 
            REFERENCES files (id) 
            ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE video_files 
            DROP CONSTRAINT FK_895749FBBF396750
        ');
        $this->addSql('
            DROP TABLE video_files
        ');
    }
}
