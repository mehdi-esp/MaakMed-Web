<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240225115427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE93993DB413D');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE93993DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE93993DB413D');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE93993DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id)');
    }
}
