<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231210100431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task ADD convert_format VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD resize_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD resize_param VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task DROP convert_format');
        $this->addSql('ALTER TABLE task DROP resize_type');
        $this->addSql('ALTER TABLE task DROP resize_param');
    }
}
