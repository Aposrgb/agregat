<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202211071714 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('delete from basket');
        $this->addSql('delete from products');
        $this->addSql(file_get_contents(__DIR__.'/data/insert_products_v2'));
        $this->addSql("alter sequence products_id_seq restart with 10781");

    }
}