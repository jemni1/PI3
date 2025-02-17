<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206160428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collecte_dechet DROP FOREIGN KEY FK_FE40281C8DCEDE65');
        $this->addSql('DROP INDEX IDX_FE40281C8DCEDE65 ON collecte_dechet');
        $this->addSql('ALTER TABLE collecte_dechet DROP id_dechet_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collecte_dechet ADD id_dechet_id INT NOT NULL');
        $this->addSql('ALTER TABLE collecte_dechet ADD CONSTRAINT FK_FE40281C8DCEDE65 FOREIGN KEY (id_dechet_id) REFERENCES recyclage_dechet (id)');
        $this->addSql('CREATE INDEX IDX_FE40281C8DCEDE65 ON collecte_dechet (id_dechet_id)');
    }
}
