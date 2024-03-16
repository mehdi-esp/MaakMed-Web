<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211145504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ADD pharmacy_id INT NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517448A94ABE2 FOREIGN KEY (pharmacy_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_906517448A94ABE2 ON invoice (pharmacy_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517448A94ABE2');
        $this->addSql('DROP INDEX IDX_906517448A94ABE2 ON invoice');
        $this->addSql('ALTER TABLE invoice DROP pharmacy_id');
    }
}
