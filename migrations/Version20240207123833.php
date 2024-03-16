<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207123833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, prescription_id INT NOT NULL, patient_id INT NOT NULL, ttotal DOUBLE PRECISION NOT NULL, discount DOUBLE PRECISION NOT NULL, reimbursement DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_9065174493DB413D (prescription_id), INDEX IDX_906517446B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prescription (id INT AUTO_INCREMENT NOT NULL, creation_date DATE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE prescription_medication (prescription_id INT NOT NULL, medication_id INT NOT NULL, quantity INT NOT NULL, instructions LONGTEXT NOT NULL, INDEX IDX_E2D09A3093DB413D (prescription_id), INDEX IDX_E2D09A302C4DE6DA (medication_id), PRIMARY KEY(prescription_id, medication_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visit (id INT AUTO_INCREMENT NOT NULL, prescription_id INT DEFAULT NULL, doctor_id INT NOT NULL, patient_id INT NOT NULL, date DATE NOT NULL, type VARCHAR(255) NOT NULL, diagnosis LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_437EE93993DB413D (prescription_id), INDEX IDX_437EE93987F4FB17 (doctor_id), INDEX IDX_437EE9396B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174493DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517446B899279 FOREIGN KEY (patient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE prescription_medication ADD CONSTRAINT FK_E2D09A3093DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id)');
        $this->addSql('ALTER TABLE prescription_medication ADD CONSTRAINT FK_E2D09A302C4DE6DA FOREIGN KEY (medication_id) REFERENCES medication (id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE93993DB413D FOREIGN KEY (prescription_id) REFERENCES prescription (id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE93987F4FB17 FOREIGN KEY (doctor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE visit ADD CONSTRAINT FK_437EE9396B899279 FOREIGN KEY (patient_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_9065174493DB413D');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517446B899279');
        $this->addSql('ALTER TABLE prescription_medication DROP FOREIGN KEY FK_E2D09A3093DB413D');
        $this->addSql('ALTER TABLE prescription_medication DROP FOREIGN KEY FK_E2D09A302C4DE6DA');
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE93993DB413D');
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE93987F4FB17');
        $this->addSql('ALTER TABLE visit DROP FOREIGN KEY FK_437EE9396B899279');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE prescription');
        $this->addSql('DROP TABLE prescription_medication');
        $this->addSql('DROP TABLE visit');
    }
}
