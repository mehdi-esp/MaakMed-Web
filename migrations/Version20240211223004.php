<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211223004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prescription_entry DROP FOREIGN KEY FK_E6F4C55F93DB413D');
        $this->addSql('ALTER TABLE prescription_entry ADD CONSTRAINT FK_E6F4C55F93DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prescription_entry DROP FOREIGN KEY FK_E6F4C55F93DB413D');
        $this->addSql('ALTER TABLE prescription_entry ADD CONSTRAINT FK_E6F4C55F93DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id)');
    }
}
