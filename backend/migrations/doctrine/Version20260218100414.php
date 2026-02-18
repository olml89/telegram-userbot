<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260218100414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change file name type from generic name custom type to filename custom type and add thumbnail to video files';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE files 
            RENAME COLUMN name TO file_name
        ');
        $this->addSql('
            ALTER TABLE files 
            ALTER file_name 
            TYPE VARCHAR(40)
        ');
        $this->addSql('
            ALTER TABLE video_files 
            ADD thumbnail VARCHAR(40) NOT NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE files 
            RENAME COLUMN file_name TO name
        ');
        $this->addSql('
            ALTER TABLE files 
            ALTER name 
            TYPE VARCHAR(50)
        ');
        $this->addSql('
            ALTER TABLE video_files 
            DROP thumbnail
        ');
    }
}
