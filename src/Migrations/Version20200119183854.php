<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200119183854 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE entry (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, caption LONGTEXT DEFAULT NULL, category_id INT NOT NULL, type_id INT NOT NULL, create_date DATETIME NOT NULL, featured TINYINT(1) NOT NULL, match_date DATETIME DEFAULT NULL, media_id VARCHAR(255) NOT NULL, rank_id INT NOT NULL, update_date DATETIME DEFAULT NULL, matched TINYINT(1) NOT NULL, vote_count INT NOT NULL, INDEX IDX_2B219D70A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, bio LONGTEXT DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, create_date DATETIME NOT NULL, name VARCHAR(50) NOT NULL, email VARCHAR(50) NOT NULL, enabled TINYINT(1) NOT NULL, featured TINYINT(1) NOT NULL, password VARCHAR(255) NOT NULL, password_change_date INT DEFAULT NULL, rank_id INT NOT NULL, update_date DATETIME DEFAULT NULL, username VARCHAR(50) NOT NULL, roles TINYTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', profile_image VARCHAR(200) DEFAULT NULL, background_image VARCHAR(200) DEFAULT NULL, followed_user_count INT NOT NULL, follower_count INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (id INT AUTO_INCREMENT NOT NULL, competition_id INT NOT NULL, entry_id INT NOT NULL, user_id INT NOT NULL, create_date DATETIME NOT NULL, INDEX IDX_5A108564BA364942 (entry_id), INDEX IDX_5A108564A76ED395 (user_id), INDEX IDX_5A1085647B39D312 (competition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE follower (id INT AUTO_INCREMENT NOT NULL, follower_id INT NOT NULL, followed_user_id INT NOT NULL, create_date DATETIME NOT NULL, invite_accepted TINYINT(1) NOT NULL, INDEX IDX_B9D60946AC24F853 (follower_id), INDEX IDX_B9D60946AF2612FD (followed_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, left_entry_id INT DEFAULT NULL, right_entry_id INT DEFAULT NULL, active TINYINT(1) NOT NULL, category_id INT NOT NULL, type_id INT NOT NULL, expire_date DATETIME NOT NULL, extended TINYINT(1) NOT NULL, featured TINYINT(1) NOT NULL, start_date DATETIME NOT NULL, winner_user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_B50A2CB11EF05F2A (left_entry_id), UNIQUE INDEX UNIQ_B50A2CB1685C110D (right_entry_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition_user (competition_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_83D0485B7B39D312 (competition_id), INDEX IDX_83D0485BA76ED395 (user_id), PRIMARY KEY(competition_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile_image (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564BA364942 FOREIGN KEY (entry_id) REFERENCES entry (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A1085647B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE follower ADD CONSTRAINT FK_B9D60946AC24F853 FOREIGN KEY (follower_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE follower ADD CONSTRAINT FK_B9D60946AF2612FD FOREIGN KEY (followed_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB11EF05F2A FOREIGN KEY (left_entry_id) REFERENCES entry (id)');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB1685C110D FOREIGN KEY (right_entry_id) REFERENCES entry (id)');
        $this->addSql('ALTER TABLE competition_user ADD CONSTRAINT FK_83D0485B7B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE competition_user ADD CONSTRAINT FK_83D0485BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564BA364942');
        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB11EF05F2A');
        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB1685C110D');
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D70A76ED395');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564A76ED395');
        $this->addSql('ALTER TABLE follower DROP FOREIGN KEY FK_B9D60946AC24F853');
        $this->addSql('ALTER TABLE follower DROP FOREIGN KEY FK_B9D60946AF2612FD');
        $this->addSql('ALTER TABLE competition_user DROP FOREIGN KEY FK_83D0485BA76ED395');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A1085647B39D312');
        $this->addSql('ALTER TABLE competition_user DROP FOREIGN KEY FK_83D0485B7B39D312');
        $this->addSql('DROP TABLE entry');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vote');
        $this->addSql('DROP TABLE follower');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE competition_user');
        $this->addSql('DROP TABLE profile_image');
    }
}
