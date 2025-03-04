<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
<<<<<<<< HEAD:migrations/Version20250301121653.php
final class Version20250301121653 extends AbstractMigration
========
final class Version20250223212934 extends AbstractMigration
>>>>>>>> a1c58de40dc03022b7c21749a037f859d403285e:migrations/Version20250223212934.php
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
<<<<<<<< HEAD:migrations/Version20250301121653.php
        $this->addSql('CREATE TABLE collecte_dechet (id INT AUTO_INCREMENT NOT NULL, recyclage_dechet_id INT DEFAULT NULL, type_dechet VARCHAR(255) NOT NULL, quantite DOUBLE PRECISION NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, INDEX IDX_FE40281CF8B50C3 (recyclage_dechet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commandes (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, id_client INT NOT NULL, id_produit INT NOT NULL, statut VARCHAR(255) NOT NULL, date DATETIME NOT NULL, prix INT NOT NULL, nom VARCHAR(90) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE culture (id_culture INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, periode_plantation VARCHAR(255) NOT NULL, periode_recoltation VARCHAR(255) NOT NULL, besoin_eau INT NOT NULL, PRIMARY KEY(id_culture)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, date_achat DATETIME NOT NULL, etat VARCHAR(255) NOT NULL, date_dernier_entretien DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE maintenance (id INT AUTO_INCREMENT NOT NULL, equipement_id INT DEFAULT NULL, type_intervention VARCHAR(255) NOT NULL, date_intervention DATETIME NOT NULL, description LONGTEXT NOT NULL, cout NUMERIC(10, 0) NOT NULL, INDEX IDX_2F84F8E9806F0F5C (equipement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, message LONGTEXT NOT NULL, is_read TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produits (id INT AUTO_INCREMENT NOT NULL, id_terrain_id INT DEFAULT NULL, quantite INT NOT NULL, nom VARCHAR(90) NOT NULL, prix INT NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_BE2DDF8C2FA70B96 (id_terrain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, sujet VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, daterec DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recyclage_dechet (id INT AUTO_INCREMENT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, quantite_recyclee DOUBLE PRECISION NOT NULL, energie_produite DOUBLE PRECISION NOT NULL, utilisation VARCHAR(255) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reponse (id_reponse INT AUTO_INCREMENT NOT NULL, idreclamation_id INT DEFAULT NULL, message VARCHAR(255) NOT NULL, daterep DATE NOT NULL, UNIQUE INDEX UNIQ_5FB6DEC73C133392 (idreclamation_id), PRIMARY KEY(id_reponse)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE terrains (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, superficie INT NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE terrain_culture (terrain_id INT NOT NULL, culture_id INT NOT NULL, INDEX IDX_CE9E15438A2D8B41 (terrain_id), INDEX IDX_CE9E1543B108249D (culture_id), PRIMARY KEY(terrain_id, culture_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, profile_picture VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
========
        $this->addSql('CREATE TABLE collecte_dechet (id INT AUTO_INCREMENT NOT NULL, type_dechet VARCHAR(255) NOT NULL, quantite DOUBLE PRECISION NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, recyclage_dechet_id INT DEFAULT NULL, INDEX IDX_FE40281CF8B50C3 (recyclage_dechet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE commandes (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, id_client INT NOT NULL, id_produit INT NOT NULL, statut VARCHAR(255) NOT NULL, date DATETIME NOT NULL, prix INT NOT NULL, nom VARCHAR(90) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE culture (id_culture INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, periode_plantation VARCHAR(255) NOT NULL, periode_recoltation VARCHAR(255) NOT NULL, besoin_eau INT NOT NULL, PRIMARY KEY(id_culture)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, date_achat DATETIME NOT NULL, etat VARCHAR(255) NOT NULL, date_dernier_entretien DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE maintenance (id INT AUTO_INCREMENT NOT NULL, type_intervention VARCHAR(255) NOT NULL, date_intervention DATETIME NOT NULL, description LONGTEXT NOT NULL, cout NUMERIC(10, 0) NOT NULL, equipement_id INT DEFAULT NULL, INDEX IDX_2F84F8E9806F0F5C (equipement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE produits (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, nom VARCHAR(90) NOT NULL, prix INT NOT NULL, image VARCHAR(255) NOT NULL, id_terrain_id INT DEFAULT NULL, INDEX IDX_BE2DDF8C2FA70B96 (id_terrain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE recyclage_dechet (id INT AUTO_INCREMENT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME NOT NULL, quantite_recyclee DOUBLE PRECISION NOT NULL, energie_produite DOUBLE PRECISION NOT NULL, utilisation VARCHAR(255) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE terrains (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, superficie INT NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE terrain_culture (terrain_id INT NOT NULL, culture_id INT NOT NULL, INDEX IDX_CE9E15438A2D8B41 (terrain_id), INDEX IDX_CE9E1543B108249D (culture_id), PRIMARY KEY(terrain_id, culture_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, profile_picture VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, reset_code VARCHAR(6) DEFAULT NULL, reset_code_expires_at DATETIME DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
>>>>>>>> a1c58de40dc03022b7c21749a037f859d403285e:migrations/Version20250223212934.php
        $this->addSql('ALTER TABLE collecte_dechet ADD CONSTRAINT FK_FE40281CF8B50C3 FOREIGN KEY (recyclage_dechet_id) REFERENCES recyclage_dechet (id)');
        $this->addSql('ALTER TABLE maintenance ADD CONSTRAINT FK_2F84F8E9806F0F5C FOREIGN KEY (equipement_id) REFERENCES equipement (id)');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8C2FA70B96 FOREIGN KEY (id_terrain_id) REFERENCES terrains (id)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC73C133392 FOREIGN KEY (idreclamation_id) REFERENCES reclamation (id)');
        $this->addSql('ALTER TABLE terrain_culture ADD CONSTRAINT FK_CE9E15438A2D8B41 FOREIGN KEY (terrain_id) REFERENCES terrains (id)');
        $this->addSql('ALTER TABLE terrain_culture ADD CONSTRAINT FK_CE9E1543B108249D FOREIGN KEY (culture_id) REFERENCES culture (id_culture)');
        $this->addSql('ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC73C133392 FOREIGN KEY (idreclamation_id) REFERENCES reclamation (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE collecte_dechet DROP FOREIGN KEY FK_FE40281CF8B50C3');
        $this->addSql('ALTER TABLE maintenance DROP FOREIGN KEY FK_2F84F8E9806F0F5C');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8C2FA70B96');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC73C133392');
        $this->addSql('ALTER TABLE terrain_culture DROP FOREIGN KEY FK_CE9E15438A2D8B41');
        $this->addSql('ALTER TABLE terrain_culture DROP FOREIGN KEY FK_CE9E1543B108249D');
        $this->addSql('DROP TABLE collecte_dechet');
        $this->addSql('DROP TABLE commandes');
        $this->addSql('DROP TABLE culture');
        $this->addSql('DROP TABLE equipement');
        $this->addSql('DROP TABLE maintenance');
<<<<<<<< HEAD:migrations/Version20250301121653.php
        $this->addSql('DROP TABLE notification');
========
>>>>>>>> a1c58de40dc03022b7c21749a037f859d403285e:migrations/Version20250223212934.php
        $this->addSql('DROP TABLE produits');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE recyclage_dechet');
        $this->addSql('DROP TABLE reponse');
        $this->addSql('DROP TABLE terrains');
        $this->addSql('DROP TABLE terrain_culture');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC73C133392');
    }
}
