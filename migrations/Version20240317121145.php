<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240317121145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paiements (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(255) NOT NULL, date_de_paiment DATETIME NOT NULL, montant INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD paiements_id INT NOT NULL');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DB985086F FOREIGN KEY (paiements_id) REFERENCES paiements (id)');
        $this->addSql('CREATE INDEX IDX_6EEAA67DB985086F ON commande (paiements_id)');
        $this->addSql('ALTER TABLE messages CHANGE message message LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE messages ADD CONSTRAINT FK_DB021E96F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_DB021E96F347EFB ON messages (produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DB985086F');
        $this->addSql('DROP TABLE paiements');
        $this->addSql('DROP INDEX IDX_6EEAA67DB985086F ON commande');
        $this->addSql('ALTER TABLE commande DROP paiements_id');
        $this->addSql('ALTER TABLE messages DROP FOREIGN KEY FK_DB021E96F347EFB');
        $this->addSql('DROP INDEX IDX_DB021E96F347EFB ON messages');
        $this->addSql('ALTER TABLE messages CHANGE message message LONGTEXT DEFAULT NULL');
    }
}
