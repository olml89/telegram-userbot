<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260218052854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image files specialization table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE image_files (
                id INT NOT NULL, 
                width INT NOT NULL, 
                height INT NOT NULL, 
                PRIMARY KEY (id)
            )
        ');
        $this->addSql('
            ALTER TABLE image_files 
            ADD CONSTRAINT FK_1170C4F0BF396750 
            FOREIGN KEY (id) 
            REFERENCES files (id) 
            ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE image_files 
            DROP CONSTRAINT FK_1170C4F0BF396750
        ');
        $this->addSql('
            DROP TABLE image_files
        ');
    }
}
