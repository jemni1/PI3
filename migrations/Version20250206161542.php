<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250206161542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commandes_produits DROP FOREIGN KEY FK_D58023F0CD11A2CF');
        $this->addSql('ALTER TABLE commandes_produits DROP FOREIGN KEY FK_D58023F08BF5C2E6');
        $this->addSql('DROP TABLE commandes_produits');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commandes_produits (commandes_id INT NOT NULL, produits_id INT NOT NULL, INDEX IDX_D58023F0CD11A2CF (produits_id), INDEX IDX_D58023F08BF5C2E6 (commandes_id), PRIMARY KEY(commandes_id, produits_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commandes_produits ADD CONSTRAINT FK_D58023F0CD11A2CF FOREIGN KEY (produits_id) REFERENCES produits (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commandes_produits ADD CONSTRAINT FK_D58023F08BF5C2E6 FOREIGN KEY (commandes_id) REFERENCES commandes (id) ON DELETE CASCADE');
    }
}
