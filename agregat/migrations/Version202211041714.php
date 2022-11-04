<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202211041714 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("insert into public.categories (id, is_popular, img, title) values
        (1, true, 'admin.stats.cfd/image5.png', 'Тормозная система'),
        (2, true, 'admin.stats.cfd/image5.png', 'Аксеуссуары и инструмент'),
        (3, true, 'admin.stats.cfd/image5.png', 'Гидравлика'),
        (4, true, 'admin.stats.cfd/image5.png', 'Пневмокомпрессоры'),
        (5, true, 'admin.stats.cfd/image5.png', 'Подвеска'),
        (6, true, 'admin.stats.cfd/image5.png', 'Подогреватели жидкостные предпусковые'),
        (7, true, 'admin.stats.cfd/image5.png', 'Приводные ремни и ролики');");
        $this->addSql("alter sequence categories_id_seq restart  with 8");
        $this->addSql(file_get_contents(__DIR__.'/data/insert_products'));
        $this->addSql("alter sequence products_id_seq restart with 7993");

    }
}