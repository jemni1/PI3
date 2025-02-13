<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206153902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE collecte_dechet (id INT AUTO_INCREMENT NOT NULL, id_terrain_id INT DEFAULT NULL, id_dechet_id INT NOT NULL, type_dechet VARCHAR(255) NOT NULL, quantite DOUBLE PRECISION NOT NULL, date_collecte DATETIME NOT NULL, INDEX IDX_FE40281C2FA70B96 (id_terrain_id), INDEX IDX_FE40281C8DCEDE65 (id_dechet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recyclage_dechet (id INT AUTO_INCREMENT NOT NULL, date_recyclage DATETIME NOT NULL, quantite_recyclee DOUBLE PRECISION NOT NULL, energie_produite DOUBLE PRECISION NOT NULL, utilisation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE terrains (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, superficie INT NOT NULL, culture VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE collecte_dechet ADD CONSTRAINT FK_FE40281C2FA70B96 FOREIGN KEY (id_terrain_id) REFERENCES terrains (id)');
        $this->addSql('ALTER TABLE collecte_dechet ADD CONSTRAINT FK_FE40281C8DCEDE65 FOREIGN KEY (id_dechet_id) REFERENCES recyclage_dechet (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collecte_dechet DROP FOREIGN KEY FK_FE40281C2FA70B96');
        $this->addSql('ALTER TABLE collecte_dechet DROP FOREIGN KEY FK_FE40281C8DCEDE65');
        $this->addSql('DROP TABLE collecte_dechet');
        $this->addSql('DROP TABLE recyclage_dechet');
        $this->addSql('DROP TABLE terrains');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
