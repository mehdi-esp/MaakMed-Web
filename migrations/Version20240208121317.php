<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208121317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prescription_entry (prescription_id INT NOT NULL, medication_id INT NOT NULL, quantity INT NOT NULL, instructions LONGTEXT NOT NULL, INDEX IDX_E6F4C55F93DB413D (prescription_id), INDEX IDX_E6F4C55F2C4DE6DA (medication_id), PRIMARY KEY(prescription_id, medication_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prescription_entry ADD CONSTRAINT FK_E6F4C55F93DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id)');
        $this->addSql('ALTER TABLE prescription_entry ADD CONSTRAINT FK_E6F4C55F2C4DE6DA FOREIGN KEY (medication_id) REFERENCES medication (id)');
        $this->addSql('ALTER TABLE prescription_medication DROP FOREIGN KEY FK_E2D09A3093DB413D');
        $this->addSql('ALTER TABLE prescription_medication DROP FOREIGN KEY FK_E2D09A302C4DE6DA');
        $this->addSql('DROP TABLE prescription_medication');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE prescription_medication (prescription_id INT NOT NULL, medication_id INT NOT NULL, quantity INT NOT NULL, instructions LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_E2D09A302C4DE6DA (medication_id), INDEX IDX_E2D09A3093DB413D (prescription_id), PRIMARY KEY(prescription_id, medication_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE prescription_medication ADD CONSTRAINT FK_E2D09A3093DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id)');
        $this->addSql('ALTER TABLE prescription_medication ADD CONSTRAINT FK_E2D09A302C4DE6DA FOREIGN KEY (medication_id) REFERENCES medication (id)');
        $this->addSql('ALTER TABLE prescription_entry DROP FOREIGN KEY FK_E6F4C55F93DB413D');
        $this->addSql('ALTER TABLE prescription_entry DROP FOREIGN KEY FK_E6F4C55F2C4DE6DA');
        $this->addSql('DROP TABLE prescription_entry');
    }
}
