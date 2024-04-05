<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240404165526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE signalisation (id INT AUTO_INCREMENT NOT NULL, utilisateur_signale_id INT NOT NULL, utilisateur_qui_signale_id INT NOT NULL, raison LONGTEXT NOT NULL, date_signalement DATETIME NOT NULL, etat VARCHAR(255) NOT NULL, action_prise VARCHAR(255) DEFAULT NULL, INDEX IDX_1BD243CD37B960BE (utilisateur_signale_id), INDEX IDX_1BD243CDBAA46E9B (utilisateur_qui_signale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE signalisation ADD CONSTRAINT FK_1BD243CD37B960BE FOREIGN KEY (utilisateur_signale_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE signalisation ADD CONSTRAINT FK_1BD243CDBAA46E9B FOREIGN KEY (utilisateur_qui_signale_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE signalisation DROP FOREIGN KEY FK_1BD243CD37B960BE');
        $this->addSql('ALTER TABLE signalisation DROP FOREIGN KEY FK_1BD243CDBAA46E9B');
        $this->addSql('DROP TABLE signalisation');
    }
}
