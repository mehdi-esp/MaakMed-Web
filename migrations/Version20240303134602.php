<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240303134602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE issue_response DROP FOREIGN KEY FK_E9D321505E7AA58C');
        $this->addSql('ALTER TABLE issue_response ADD CONSTRAINT FK_E9D321505E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE issue_response DROP FOREIGN KEY FK_E9D321505E7AA58C');
        $this->addSql('ALTER TABLE issue_response ADD CONSTRAINT FK_E9D321505E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id)');
    }
}
