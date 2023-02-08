<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230207235605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD profile_picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD phone_number VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD social_number VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP profile_picture');
        $this->addSql('ALTER TABLE "user" DROP phone_number');
        $this->addSql('ALTER TABLE "user" DROP social_number');
    }
}
