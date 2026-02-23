<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260220140812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add pdf files specialization table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE pdf_files (
                id INT NOT NULL, 
                PRIMARY KEY (id)
            )
        ');
        $this->addSql('
            ALTER TABLE pdf_files 
            ADD CONSTRAINT FK_97A20D83BF396750 
            FOREIGN KEY (id) 
            REFERENCES files (id) 
            ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE pdf_files 
            DROP CONSTRAINT FK_97A20D83BF396750
        ');
        $this->addSql('
            DROP TABLE pdf_files
        ');
    }
}
