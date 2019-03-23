<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190321220445 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL, CHANGE password_change_date password_change_date INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT NULL, CHANGE profile_image profile_image VARCHAR(200) DEFAULT NULL, CHANGE background_image background_image VARCHAR(200) DEFAULT NULL');
        $this->addSql('ALTER TABLE entry CHANGE match_date match_date DATETIME DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE follower DROP FOREIGN KEY FK_B9D60946A76ED395');
        $this->addSql('DROP INDEX IDX_B9D60946A76ED395 ON follower');
        $this->addSql('ALTER TABLE follower CHANGE user_id follower_id INT NOT NULL');
        $this->addSql('ALTER TABLE follower ADD CONSTRAINT FK_B9D60946AC24F853 FOREIGN KEY (follower_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B9D60946AC24F853 ON follower (follower_id)');
        $this->addSql('ALTER TABLE profile_image CHANGE url url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE entry CHANGE match_date match_date DATETIME DEFAULT \'NULL\', CHANGE update_date update_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE follower DROP FOREIGN KEY FK_B9D60946AC24F853');
        $this->addSql('DROP INDEX IDX_B9D60946AC24F853 ON follower');
        $this->addSql('ALTER TABLE follower CHANGE follower_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE follower ADD CONSTRAINT FK_B9D60946A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B9D60946A76ED395 ON follower (user_id)');
        $this->addSql('ALTER TABLE profile_image CHANGE url url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password_change_date password_change_date INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT \'NULL\', CHANGE profile_image profile_image VARCHAR(200) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE background_image background_image VARCHAR(200) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
