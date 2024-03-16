<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227202819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice_entry (invoice_id INT NOT NULL, medication_id INT NOT NULL, quantity INT NOT NULL, cost DOUBLE PRECISION NOT NULL, INDEX IDX_16FBCDC52989F1FD (invoice_id), INDEX IDX_16FBCDC52C4DE6DA (medication_id), PRIMARY KEY(invoice_id, medication_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice_entry ADD CONSTRAINT FK_16FBCDC52989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE invoice_entry ADD CONSTRAINT FK_16FBCDC52C4DE6DA FOREIGN KEY (medication_id) REFERENCES medication (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE invoice DROP total');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice_entry DROP FOREIGN KEY FK_16FBCDC52989F1FD');
        $this->addSql('ALTER TABLE invoice_entry DROP FOREIGN KEY FK_16FBCDC52C4DE6DA');
        $this->addSql('DROP TABLE invoice_entry');
        $this->addSql('ALTER TABLE invoice ADD total DOUBLE PRECISION NOT NULL');
    }
}
