<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240410205240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE signalisation ADD CONSTRAINT FK_1BD243CDF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_1BD243CDF347EFB ON signalisation (produit_id)');
        $this->addSql('ALTER TABLE user ADD bloquer TINYINT(1) DEFAULT NULL, ADD date_expiration_blocage DATETIME DEFAULT NULL');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE signalisation DROP FOREIGN KEY FK_1BD243CDF347EFB');
        $this->addSql('DROP INDEX IDX_1BD243CDF347EFB ON signalisation');
        $this->addSql('ALTER TABLE user DROP bloquer, DROP date_expiration_blocage');
    }
}
