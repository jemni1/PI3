<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206155304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commandes (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, statut VARCHAR(255) NOT NULL, date DATETIME NOT NULL, prix INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commandes_produits (commandes_id INT NOT NULL, produits_id INT NOT NULL, INDEX IDX_D58023F08BF5C2E6 (commandes_id), INDEX IDX_D58023F0CD11A2CF (produits_id), PRIMARY KEY(commandes_id, produits_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produits (id INT AUTO_INCREMENT NOT NULL, id_terrain_id INT DEFAULT NULL, quantite INT NOT NULL, nom VARCHAR(90) NOT NULL, prix INT NOT NULL, INDEX IDX_BE2DDF8C2FA70B96 (id_terrain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE terrains (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, superficie INT NOT NULL, culture VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commandes_produits ADD CONSTRAINT FK_D58023F08BF5C2E6 FOREIGN KEY (commandes_id) REFERENCES commandes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commandes_produits ADD CONSTRAINT FK_D58023F0CD11A2CF FOREIGN KEY (produits_id) REFERENCES produits (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8C2FA70B96 FOREIGN KEY (id_terrain_id) REFERENCES terrains (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commandes_produits DROP FOREIGN KEY FK_D58023F08BF5C2E6');
        $this->addSql('ALTER TABLE commandes_produits DROP FOREIGN KEY FK_D58023F0CD11A2CF');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8C2FA70B96');
        $this->addSql('DROP TABLE commandes');
        $this->addSql('DROP TABLE commandes_produits');
        $this->addSql('DROP TABLE produits');
        $this->addSql('DROP TABLE terrains');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
