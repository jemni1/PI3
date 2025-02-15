<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214185701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collecte_dechet CHANGE date_fin date_fin DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE recyclage_dechet ADD date_fin DATETIME NOT NULL, CHANGE date_recyclage date_debut DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collecte_dechet CHANGE date_fin date_fin DATETIME NOT NULL');
        $this->addSql('ALTER TABLE recyclage_dechet ADD date_recyclage DATETIME NOT NULL, DROP date_debut, DROP date_fin');
    }
}
