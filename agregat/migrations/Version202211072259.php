<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202211072259 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('update products set categories_id = 1 where id > 7300 and id < 7500');
        $this->addSql('update products set categories_id = 2 where id > 7500 and id < 7800');
    }
}