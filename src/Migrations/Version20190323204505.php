<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190323204505 extends AbstractMigration
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
        $this->addSql('ALTER TABLE competition ADD left_entry_id INT DEFAULT NULL, ADD right_entry_id INT DEFAULT NULL, CHANGE winner_user_id winner_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB11EF05F2A FOREIGN KEY (left_entry_id) REFERENCES entry (id)');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB1685C110D FOREIGN KEY (right_entry_id) REFERENCES entry (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B50A2CB11EF05F2A ON competition (left_entry_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B50A2CB1685C110D ON competition (right_entry_id)');
        $this->addSql('ALTER TABLE profile_image CHANGE url url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB11EF05F2A');
        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB1685C110D');
        $this->addSql('DROP INDEX UNIQ_B50A2CB11EF05F2A ON competition');
        $this->addSql('DROP INDEX UNIQ_B50A2CB1685C110D ON competition');
        $this->addSql('ALTER TABLE competition DROP left_entry_id, DROP right_entry_id, CHANGE winner_user_id winner_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE entry CHANGE match_date match_date DATETIME DEFAULT \'NULL\', CHANGE update_date update_date DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE profile_image CHANGE url url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password_change_date password_change_date INT DEFAULT NULL, CHANGE update_date update_date DATETIME DEFAULT \'NULL\', CHANGE profile_image profile_image VARCHAR(200) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE background_image background_image VARCHAR(200) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
