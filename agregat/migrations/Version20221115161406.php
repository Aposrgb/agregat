<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221115161406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE sub_categories_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE sub_categories (id INT NOT NULL, category_id INT DEFAULT NULL, img VARCHAR(255) DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1638D5A512469DE2 ON sub_categories (category_id)');
        $this->addSql('ALTER TABLE sub_categories ADD CONSTRAINT FK_1638D5A512469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE categories ALTER img DROP NOT NULL');
        $this->addSql('ALTER TABLE products ADD sub_categories_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A6DBFD369 FOREIGN KEY (sub_categories_id) REFERENCES sub_categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A6DBFD369 ON products (sub_categories_id)');
        $this->addSql('delete from products');
        $this->addSql('delete from categories');
        $this->addSql(file_get_contents(__DIR__.'/data/insert_products_v2'));
        $this->addSql(file_get_contents(__DIR__.'/data/insert_category_v2.txt'));
        $this->addSql(file_get_contents(__DIR__.'/data/insert_sub_categories.txt'));
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE products DROP CONSTRAINT FK_B3BA5A5A6DBFD369');
        $this->addSql('DROP SEQUENCE sub_categories_id_seq CASCADE');
        $this->addSql('ALTER TABLE sub_categories DROP CONSTRAINT FK_1638D5A512469DE2');
        $this->addSql('DROP TABLE sub_categories');
        $this->addSql('DROP INDEX IDX_B3BA5A5A6DBFD369');
        $this->addSql('ALTER TABLE products DROP sub_categories_id');
        $this->addSql('ALTER TABLE categories ALTER img SET NOT NULL');
    }
}
