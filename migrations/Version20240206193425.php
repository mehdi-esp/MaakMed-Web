<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240206193425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE issue (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, creation_date DATE NOT NULL, INDEX IDX_12AD233EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE issue_response (id INT AUTO_INCREMENT NOT NULL, issue_id INT NOT NULL, respondent_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, creation_date DATE NOT NULL, INDEX IDX_E9D321505E7AA58C (issue_id), INDEX IDX_E9D32150CE80CD19 (respondent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE issue_response ADD CONSTRAINT FK_E9D321505E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id)');
        $this->addSql('ALTER TABLE issue_response ADD CONSTRAINT FK_E9D32150CE80CD19 FOREIGN KEY (respondent_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233EA76ED395');
        $this->addSql('ALTER TABLE issue_response DROP FOREIGN KEY FK_E9D321505E7AA58C');
        $this->addSql('ALTER TABLE issue_response DROP FOREIGN KEY FK_E9D32150CE80CD19');
        $this->addSql('DROP TABLE issue');
        $this->addSql('DROP TABLE issue_response');
    }
}
