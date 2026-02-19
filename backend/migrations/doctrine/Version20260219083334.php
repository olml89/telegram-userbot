<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260219083334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add timestamps to categories, tags, files and contents';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE categories 
            ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        ');
        $this->addSql('
            ALTER TABLE categories 
            ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        ');
        $this->addSql('
            ALTER TABLE tags 
            ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        ');
        $this->addSql('
            ALTER TABLE tags 
            ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        ');
        $this->addSql('
            ALTER TABLE files 
            ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        ');
        $this->addSql('
            ALTER TABLE files 
            ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        ');
        $this->addSql('
            ALTER TABLE contents 
            ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        ');
        $this->addSql('
            ALTER TABLE contents 
            ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE categories 
            DROP created_at
        ');
        $this->addSql('
            ALTER TABLE categories 
            DROP updated_at
        ');
        $this->addSql('
            ALTER TABLE tags 
            DROP created_at
        ');
        $this->addSql('
            ALTER TABLE tags 
            DROP updated_at
        ');
        $this->addSql('
            ALTER TABLE files 
            DROP created_at
        ');
        $this->addSql('
            ALTER TABLE files 
            DROP updated_at
        ');
        $this->addSql('
            ALTER TABLE contents 
            DROP created_at
        ');
        $this->addSql('
            ALTER TABLE contents 
            DROP updated_at
        ');
    }
}
