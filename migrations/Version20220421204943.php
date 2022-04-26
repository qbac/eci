<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220421204943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493C2DD5AA');
        $this->addSql('DROP INDEX idx_8d93d6493c2dd5aa ON user');
        $this->addSql('CREATE INDEX IDX_8D93D649BC18698D ON user (employ_id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493C2DD5AA FOREIGN KEY (employ_id) REFERENCES employ (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649BC18698D');
        $this->addSql('DROP INDEX idx_8d93d649bc18698d ON user');
        $this->addSql('CREATE INDEX IDX_8D93D6493C2DD5AA ON user (employ_id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649BC18698D FOREIGN KEY (employ_id) REFERENCES employ (id)');
    }
}
