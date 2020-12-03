<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201203095411 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('CREATE FULLTEXT INDEX IDX_1CF73D31D3CB5CA7 ON product (product_name)');
        $this->addSql('DROP INDEX idx_d34a04ad12469de2 ON product');
        $this->addSql('CREATE INDEX IDX_1CF73D3112469DE2 ON product (category_id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_1CF73D31D3CB5CA7 ON Product');
        $this->addSql('ALTER TABLE Product DROP FOREIGN KEY FK_1CF73D3112469DE2');
        $this->addSql('DROP INDEX idx_1cf73d3112469de2 ON Product');
        $this->addSql('CREATE INDEX IDX_D34A04AD12469DE2 ON Product (category_id)');
        $this->addSql('ALTER TABLE Product ADD CONSTRAINT FK_1CF73D3112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }
}
