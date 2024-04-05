<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240402204548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS=0');
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DB985086F FOREIGN KEY (paiements_id) REFERENCES paiements (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DB985086F ON commande (paiements_id)');
        $this->addSql('ALTER TABLE produit ADD statut VARCHAR(255) NOT NULL');
        $this->addSql('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DB985086F');
        $this->addSql('DROP INDEX IDX_6EEAA67DB985086F ON commande');
        $this->addSql('ALTER TABLE produit DROP statut');
    }
}
