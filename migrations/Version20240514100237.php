<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240514100237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_qualification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, qualification_id INT NOT NULL, date_start DATE DEFAULT NULL, date_end DATE DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, INDEX IDX_599C5D9E79F37AE5 (user_id), INDEX IDX_599C5D9EF9FFBBE9 (qualification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_qualification ADD CONSTRAINT FK_599C5D9E79F37AE5 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_qualification ADD CONSTRAINT FK_599C5D9EF9FFBBE9 FOREIGN KEY (qualification_id) REFERENCES qualification (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_qualification');
    }
}
