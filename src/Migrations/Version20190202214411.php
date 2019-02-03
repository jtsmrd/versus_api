<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190202214411 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE entry (id INT AUTO_INCREMENT NOT NULL, caption LONGTEXT DEFAULT NULL, category_id INT NOT NULL, type_id INT NOT NULL, create_date DATETIME NOT NULL, featured TINYINT(1) NOT NULL, match_date DATETIME DEFAULT NULL, media_id VARCHAR(255) NOT NULL, rank_id INT NOT NULL, update_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL, CHANGE password_change_date password_change_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE entry');
        $this->addSql('ALTER TABLE user CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password_change_date password_change_date DATETIME DEFAULT \'NULL\'');
    }
}
