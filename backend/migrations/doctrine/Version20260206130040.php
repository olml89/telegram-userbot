<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260206130040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make files hold a reference to its content';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE files 
            ADD content_id INT DEFAULT NULL
        ');
        $this->addSql('
            ALTER TABLE files 
            ADD CONSTRAINT FK_635405984A0A3ED 
            FOREIGN KEY (content_id) 
            REFERENCES contents (id) 
            ON DELETE CASCADE
        ');
        $this->addSql('
            CREATE INDEX IDX_635405984A0A3ED 
            ON files (content_id)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE files 
            DROP CONSTRAINT FK_635405984A0A3ED
        ');
        $this->addSql('
            DROP INDEX IDX_635405984A0A3ED
        ');
        $this->addSql('
            ALTER TABLE files 
            DROP content_id
        ');
    }
}
