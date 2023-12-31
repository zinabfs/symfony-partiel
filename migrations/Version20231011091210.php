<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231011091210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupe DROP FOREIGN KEY FK_4B98C217E3C61F9');
        $this->addSql('DROP INDEX IDX_4B98C217E3C61F9 ON groupe');
        $this->addSql('ALTER TABLE groupe ADD owner VARCHAR(255) NOT NULL, DROP owner_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupe ADD owner_id INT DEFAULT NULL, DROP owner');
        $this->addSql('ALTER TABLE groupe ADD CONSTRAINT FK_4B98C217E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4B98C217E3C61F9 ON groupe (owner_id)');
    }
}
