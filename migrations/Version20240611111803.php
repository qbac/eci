<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611111803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_qualification DROP FOREIGN KEY FK_599C5D9EF9FFBBE9');
        $this->addSql('ALTER TABLE user_qualification DROP FOREIGN KEY FK_599C5D9E79F37AE5');
        $this->addSql('ALTER TABLE user_qualification ADD file_name VARCHAR(255) DEFAULT NULL, ADD uploaded_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP INDEX idx_599c5d9e79f37ae5 ON user_qualification');
        $this->addSql('CREATE INDEX IDX_599C5D9EA76ED395 ON user_qualification (user_id)');
        $this->addSql('DROP INDEX idx_599c5d9ef9ffbbe9 ON user_qualification');
        $this->addSql('CREATE INDEX IDX_599C5D9E1A75EE38 ON user_qualification (qualification_id)');
        $this->addSql('ALTER TABLE user_qualification ADD CONSTRAINT FK_599C5D9EF9FFBBE9 FOREIGN KEY (qualification_id) REFERENCES qualification (id)');
        $this->addSql('ALTER TABLE user_qualification ADD CONSTRAINT FK_599C5D9E79F37AE5 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_qualification DROP FOREIGN KEY FK_599C5D9EA76ED395');
        $this->addSql('ALTER TABLE user_qualification DROP FOREIGN KEY FK_599C5D9E1A75EE38');
        $this->addSql('ALTER TABLE user_qualification DROP file_name, DROP uploaded_at');
        $this->addSql('DROP INDEX idx_599c5d9e1a75ee38 ON user_qualification');
        $this->addSql('CREATE INDEX IDX_599C5D9EF9FFBBE9 ON user_qualification (qualification_id)');
        $this->addSql('DROP INDEX idx_599c5d9ea76ed395 ON user_qualification');
        $this->addSql('CREATE INDEX IDX_599C5D9E79F37AE5 ON user_qualification (user_id)');
        $this->addSql('ALTER TABLE user_qualification ADD CONSTRAINT FK_599C5D9EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_qualification ADD CONSTRAINT FK_599C5D9E1A75EE38 FOREIGN KEY (qualification_id) REFERENCES qualification (id)');
    }
}
