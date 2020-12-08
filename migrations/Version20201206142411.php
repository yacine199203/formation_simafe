<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201206142411 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Product (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, product_name VARCHAR(255) NOT NULL, png VARCHAR(255) NOT NULL, pdf VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_1CF73D3112469DE2 (category_id), FULLTEXT INDEX IDX_1CF73D31D3CB5CA7 (product_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, category_name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characteristics (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, characteristics VARCHAR(255) NOT NULL, INDEX IDX_7037B1564584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `condition` (id INT AUTO_INCREMENT NOT NULL, recruitement_id INT DEFAULT NULL, liste VARCHAR(255) NOT NULL, INDEX IDX_BDD6884374FB9C61 (recruitement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, job VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_product (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, job_id INT DEFAULT NULL, INDEX IDX_49A5B3364584665A (product_id), INDEX IDX_49A5B336BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, unsubscribe TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE production_image (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_DF3CC0699395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE production_job (id INT AUTO_INCREMENT NOT NULL, job_id INT DEFAULT NULL, customer VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_505A22D2BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recruitement (id INT AUTO_INCREMENT NOT NULL, job VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sliders (id INT AUTO_INCREMENT NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, pass VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Product ADD CONSTRAINT FK_1CF73D3112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE characteristics ADD CONSTRAINT FK_7037B1564584665A FOREIGN KEY (product_id) REFERENCES Product (id)');
        $this->addSql('ALTER TABLE `condition` ADD CONSTRAINT FK_BDD6884374FB9C61 FOREIGN KEY (recruitement_id) REFERENCES recruitement (id)');
        $this->addSql('ALTER TABLE job_product ADD CONSTRAINT FK_49A5B3364584665A FOREIGN KEY (product_id) REFERENCES Product (id)');
        $this->addSql('ALTER TABLE job_product ADD CONSTRAINT FK_49A5B336BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE production_image ADD CONSTRAINT FK_DF3CC0699395C3F3 FOREIGN KEY (customer_id) REFERENCES production_job (id)');
        $this->addSql('ALTER TABLE production_job ADD CONSTRAINT FK_505A22D2BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE characteristics DROP FOREIGN KEY FK_7037B1564584665A');
        $this->addSql('ALTER TABLE job_product DROP FOREIGN KEY FK_49A5B3364584665A');
        $this->addSql('ALTER TABLE Product DROP FOREIGN KEY FK_1CF73D3112469DE2');
        $this->addSql('ALTER TABLE job_product DROP FOREIGN KEY FK_49A5B336BE04EA9');
        $this->addSql('ALTER TABLE production_job DROP FOREIGN KEY FK_505A22D2BE04EA9');
        $this->addSql('ALTER TABLE production_image DROP FOREIGN KEY FK_DF3CC0699395C3F3');
        $this->addSql('ALTER TABLE `condition` DROP FOREIGN KEY FK_BDD6884374FB9C61');
        $this->addSql('DROP TABLE Product');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE characteristics');
        $this->addSql('DROP TABLE `condition`');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE job_product');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP TABLE production_image');
        $this->addSql('DROP TABLE production_job');
        $this->addSql('DROP TABLE recruitement');
        $this->addSql('DROP TABLE sliders');
        $this->addSql('DROP TABLE user');
    }
}
