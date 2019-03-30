<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190324024412 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE competition_user (competition_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_83D0485B7B39D312 (competition_id), INDEX IDX_83D0485BA76ED395 (user_id), PRIMARY KEY(competition_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competition_user ADD CONSTRAINT FK_83D0485B7B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competition_user ADD CONSTRAINT FK_83D0485BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL, CHANGE password_change_date password_change_date INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT NULL, CHANGE profile_image profile_image VARCHAR(200) DEFAULT NULL, CHANGE background_image background_image VARCHAR(200) DEFAULT NULL');
        $this->addSql('ALTER TABLE entry CHANGE match_date match_date DATETIME DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE competition CHANGE left_entry_id left_entry_id INT DEFAULT NULL, CHANGE right_entry_id right_entry_id INT DEFAULT NULL, CHANGE winner_user_id winner_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE profile_image CHANGE url url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE competition_user');
        $this->addSql('ALTER TABLE competition CHANGE left_entry_id left_entry_id INT DEFAULT NULL, CHANGE right_entry_id right_entry_id INT DEFAULT NULL, CHANGE winner_user_id winner_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE entry CHANGE match_date match_date DATETIME DEFAULT \'NULL\', CHANGE update_date update_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE profile_image CHANGE url url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password_change_date password_change_date INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT \'NULL\', CHANGE profile_image profile_image VARCHAR(200) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE background_image background_image VARCHAR(200) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
