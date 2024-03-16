<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240225212550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prescription_entry DROP FOREIGN KEY FK_E6F4C55F2C4DE6DA');
        $this->addSql('ALTER TABLE prescription_entry ADD CONSTRAINT FK_E6F4C55F2C4DE6DA FOREIGN KEY (medication_id) REFERENCES medication (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prescription_entry DROP FOREIGN KEY FK_E6F4C55F2C4DE6DA');
        $this->addSql('ALTER TABLE prescription_entry ADD CONSTRAINT FK_E6F4C55F2C4DE6DA FOREIGN KEY (medication_id) REFERENCES medication (id)');
    }
}
