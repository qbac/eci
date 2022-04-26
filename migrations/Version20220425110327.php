<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220425110327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, date_start DATE NOT NULL, date_end DATE NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_time (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, project_id INT NOT NULL, employ_id INT DEFAULT NULL, work_date DATE NOT NULL, work_time TIME NOT NULL, INDEX IDX_9657297DA76ED395 (user_id), INDEX IDX_9657297D166D1F9C (project_id), INDEX IDX_9657297DBC18698D (employ_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE work_time ADD CONSTRAINT FK_9657297DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE work_time ADD CONSTRAINT FK_9657297D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE work_time ADD CONSTRAINT FK_9657297DBC18698D FOREIGN KEY (employ_id) REFERENCES employ (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_time DROP FOREIGN KEY FK_9657297D166D1F9C');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE work_time');
    }
}
