<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201117163150 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_product ADD job_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job_product ADD CONSTRAINT FK_49A5B336BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('CREATE INDEX IDX_49A5B336BE04EA9 ON job_product (job_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_product DROP FOREIGN KEY FK_49A5B336BE04EA9');
        $this->addSql('DROP INDEX IDX_49A5B336BE04EA9 ON job_product');
        $this->addSql('ALTER TABLE job_product DROP job_id');
    }
}
