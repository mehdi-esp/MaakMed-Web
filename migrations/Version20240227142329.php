<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227142329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_9065174493DB413D');
        $this->addSql('DROP INDEX UNIQ_9065174493DB413D ON invoice');
        $this->addSql('ALTER TABLE invoice CHANGE prescription_id patient_id INT NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517446B899279 FOREIGN KEY (patient_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_906517446B899279 ON invoice (patient_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517446B899279');
        $this->addSql('DROP INDEX IDX_906517446B899279 ON invoice');
        $this->addSql('ALTER TABLE invoice CHANGE patient_id prescription_id INT NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174493DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9065174493DB413D ON invoice (prescription_id)');
    }
}
