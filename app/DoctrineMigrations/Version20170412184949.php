<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170412184949 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE assignment (id INT AUTO_INCREMENT NOT NULL, student_id INT DEFAULT NULL, homework_id INT DEFAULT NULL, date DATETIME NOT NULL, grade INT DEFAULT NULL, work VARCHAR(255) DEFAULT NULL, INDEX IDX_30C544BACB944F1A (student_id), INDEX IDX_30C544BAB203DDE5 (homework_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BACB944F1A FOREIGN KEY (student_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE assignment ADD CONSTRAINT FK_30C544BAB203DDE5 FOREIGN KEY (homework_id) REFERENCES homework (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE assignment');
    }
}
