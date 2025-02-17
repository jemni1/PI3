<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210153049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE terrain_culture (terrain_id INT NOT NULL, culture_id INT NOT NULL, INDEX IDX_CE9E15438A2D8B41 (terrain_id), INDEX IDX_CE9E1543B108249D (culture_id), PRIMARY KEY(terrain_id, culture_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE terrain_culture ADD CONSTRAINT FK_CE9E15438A2D8B41 FOREIGN KEY (terrain_id) REFERENCES terrains (id_terrain)');
        $this->addSql('ALTER TABLE terrain_culture ADD CONSTRAINT FK_CE9E1543B108249D FOREIGN KEY (culture_id) REFERENCES culture (id_culture)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE terrain_culture DROP FOREIGN KEY FK_CE9E15438A2D8B41');
        $this->addSql('ALTER TABLE terrain_culture DROP FOREIGN KEY FK_CE9E1543B108249D');
        $this->addSql('DROP TABLE terrain_culture');
    }
}
