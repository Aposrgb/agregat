<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202211072259 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(file_get_contents(__DIR__.'/data/insert_news'));
        $this->addSql('alter sequence news_id_seq restart  with 6');
        $this->addSql('Delete from category');
        $this->addSql(file_get_contents(__DIR__ . '/data/insert_category.txt'));
        $this->addSql('update products set categories_id = 1 where id > 7300 and id < 7500');
        $this->addSql('update products set categories_id = 2 where id > 7500 and id < 7800');
    }
}