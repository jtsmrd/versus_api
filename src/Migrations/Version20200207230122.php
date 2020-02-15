<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200207230122 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE leaderboard (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, feature_image VARCHAR(255) DEFAULT NULL, background_image VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) NOT NULL, result_limit INT NOT NULL, UNIQUE INDEX UNIQ_182E5253C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE leader (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, leaderboard_type_id INT NOT NULL, win_count INT NOT NULL, vote_count INT NOT NULL, INDEX IDX_F5E3EAD7A76ED395 (user_id), INDEX IDX_F5E3EAD7B3D1D950 (leaderboard_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE leaderboard_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE leaderboard ADD CONSTRAINT FK_182E5253C54C8C93 FOREIGN KEY (type_id) REFERENCES leaderboard_type (id)');
        $this->addSql('ALTER TABLE leader ADD CONSTRAINT FK_F5E3EAD7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE leader ADD CONSTRAINT FK_F5E3EAD7B3D1D950 FOREIGN KEY (leaderboard_type_id) REFERENCES leaderboard_type (id)');
        $this->addSql('ALTER TABLE entry CHANGE match_date match_date DATETIME DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL, CHANGE password_change_date password_change_date INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT NULL, CHANGE profile_image profile_image VARCHAR(200) DEFAULT NULL, CHANGE background_image background_image VARCHAR(200) DEFAULT NULL');
        $this->addSql('ALTER TABLE competition CHANGE left_entry_id left_entry_id INT DEFAULT NULL, CHANGE right_entry_id right_entry_id INT DEFAULT NULL, CHANGE winner_user_id winner_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE profile_image CHANGE url url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE leaderboard DROP FOREIGN KEY FK_182E5253C54C8C93');
        $this->addSql('ALTER TABLE leader DROP FOREIGN KEY FK_F5E3EAD7B3D1D950');
        $this->addSql('DROP TABLE leaderboard');
        $this->addSql('DROP TABLE leader');
        $this->addSql('DROP TABLE leaderboard_type');
        $this->addSql('ALTER TABLE competition CHANGE left_entry_id left_entry_id INT DEFAULT NULL, CHANGE right_entry_id right_entry_id INT DEFAULT NULL, CHANGE winner_user_id winner_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE entry CHANGE match_date match_date DATETIME DEFAULT \'NULL\', CHANGE update_date update_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE profile_image CHANGE url url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password_change_date password_change_date INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT \'NULL\', CHANGE profile_image profile_image VARCHAR(200) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE background_image background_image VARCHAR(200) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
