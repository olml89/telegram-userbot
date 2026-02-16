<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\Infrastructure\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260209104250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Make Category name unique and change price type from double to numeric';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE UNIQUE INDEX UNIQ_3AF346685E237E06 
            ON categories (name)
        ');
        $this->addSql('
            ALTER TABLE contents 
            ALTER price 
            TYPE NUMERIC(10, 2)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            DROP INDEX UNIQ_3AF346685E237E06
        ');
        $this->addSql('
            ALTER TABLE contents 
            ALTER price 
            TYPE DOUBLE PRECISION
        ');
    }
}
