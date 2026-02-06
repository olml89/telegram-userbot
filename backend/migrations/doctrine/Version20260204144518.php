<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260204144518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modify content to match the specifications';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE content_tag (
                content_id INT NOT NULL, 
                tag_id INT NOT NULL, 
                PRIMARY KEY (content_id, tag_id)
            )
        ');
        $this->addSql('
            CREATE INDEX IDX_B662E17684A0A3ED 
            ON content_tag (content_id)
        ');
        $this->addSql('
            CREATE INDEX IDX_B662E176BAD26311 
            ON content_tag (tag_id)
        ');
        $this->addSql('
            ALTER TABLE content_tag 
            ADD CONSTRAINT FK_B662E17684A0A3ED 
            FOREIGN KEY (content_id) 
            REFERENCES contents (id)
        ');
        $this->addSql('
            ALTER TABLE content_tag 
            ADD CONSTRAINT FK_B662E176BAD26311 
            FOREIGN KEY (tag_id) 
            REFERENCES tags (id)
        ');
        $this->addSql('
            ALTER TABLE contents 
            ADD intensity INT NOT NULL
        ');
        $this->addSql('
            ALTER TABLE contents 
            ADD price DOUBLE PRECISION NOT NULL
        ');
        $this->addSql('
            ALTER TABLE contents 
            ADD sales INT NOT NULL
        ');
        $this->addSql('
            ALTER TABLE contents 
            ADD language VARCHAR(2) NOT NULL
        ');
        $this->addSql('
            ALTER TABLE contents 
            ADD mode VARCHAR(20) NOT NULL
        ');
        $this->addSql('
            ALTER TABLE contents 
            ADD status VARCHAR(20) NOT NULL
        ');
        $this->addSql('
            ALTER TABLE contents 
            ADD category_id INT NOT NULL
        ');
        $this->addSql('
            ALTER TABLE contents 
            DROP tags
        ');
        $this->addSql('
            ALTER TABLE contents 
            ALTER description 
            SET NOT NULL
        ');
        $this->addSql('
            ALTER TABLE contents 
            RENAME COLUMN name TO title
        ');
        $this->addSql('
            ALTER TABLE contents 
            ADD CONSTRAINT FK_B4FA117712469DE2 
            FOREIGN KEY (category_id) 
            REFERENCES categories (id)
        ');
        $this->addSql('
            CREATE UNIQUE INDEX UNIQ_B4FA11772B36786B 
            ON contents (title)
        ');
        $this->addSql('
            CREATE INDEX IDX_B4FA117712469DE2 
            ON contents (category_id)
        ');
        $this->addSql('
            CREATE UNIQUE INDEX UNIQ_6FBC94265E237E06 
            ON tags (name)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE content_tag 
            DROP CONSTRAINT FK_B662E17684A0A3ED
        ');
        $this->addSql('
            ALTER TABLE content_tag 
            DROP CONSTRAINT FK_B662E176BAD26311
        ');
        $this->addSql('
            DROP TABLE content_tag
        ');
        $this->addSql('
            ALTER TABLE contents 
            DROP CONSTRAINT FK_B4FA117712469DE2
        ');
        $this->addSql('
            DROP INDEX UNIQ_B4FA11772B36786B
        ');
        $this->addSql('
            DROP INDEX IDX_B4FA117712469DE2
        ');
        $this->addSql('
            ALTER TABLE contents
            ADD tags JSON NOT NULL
        ');
        $this->addSql('
            ALTER TABLE contents 
            DROP intensity
        ');
        $this->addSql('
            ALTER TABLE contents 
            DROP price
        ');
        $this->addSql('
            ALTER TABLE contents 
            DROP sales
        ');
        $this->addSql('
            ALTER TABLE contents 
            DROP language
        ');
        $this->addSql('
            ALTER TABLE contents 
            DROP mode
        ');
        $this->addSql('
            ALTER TABLE contents 
            DROP status
        ');
        $this->addSql('
            ALTER TABLE contents 
            DROP category_id
        ');
        $this->addSql('
            ALTER TABLE contents 
            ALTER description 
            DROP NOT NULL
        ');
        $this->addSql('
            ALTER TABLE contents 
            RENAME COLUMN title TO name
        ');
        $this->addSql('
            DROP INDEX UNIQ_6FBC94265E237E06
        ');
    }
}
