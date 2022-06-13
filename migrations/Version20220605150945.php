<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220605150945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial Entities';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cigarettes (id INT AUTO_INCREMENT NOT NULL, brand VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE drink (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) DEFAULT NULL, alcohol TINYINT(1) DEFAULT NULL, hot TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE house (id INT AUTO_INCREMENT NOT NULL, color VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pet (id INT AUTO_INCREMENT NOT NULL, species VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, breed VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE scientist (id INT AUTO_INCREMENT NOT NULL, pet_id INT DEFAULT NULL, drink_id INT DEFAULT NULL, house_id INT DEFAULT NULL, cigarettes_id INT DEFAULT NULL, nationality VARCHAR(255) DEFAULT NULL, INDEX IDX_E1774A61966F7FB6 (pet_id), INDEX IDX_E1774A6136AA4BB4 (drink_id), INDEX IDX_E1774A616BB74515 (house_id), INDEX IDX_E1774A6131EE5F70 (cigarettes_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE scientist ADD CONSTRAINT FK_E1774A61966F7FB6 FOREIGN KEY (pet_id) REFERENCES pet (id)');
        $this->addSql('ALTER TABLE scientist ADD CONSTRAINT FK_E1774A6136AA4BB4 FOREIGN KEY (drink_id) REFERENCES drink (id)');
        $this->addSql('ALTER TABLE scientist ADD CONSTRAINT FK_E1774A616BB74515 FOREIGN KEY (house_id) REFERENCES house (id)');
        $this->addSql('ALTER TABLE scientist ADD CONSTRAINT FK_E1774A6131EE5F70 FOREIGN KEY (cigarettes_id) REFERENCES cigarettes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scientist DROP FOREIGN KEY FK_E1774A6131EE5F70');
        $this->addSql('ALTER TABLE scientist DROP FOREIGN KEY FK_E1774A6136AA4BB4');
        $this->addSql('ALTER TABLE scientist DROP FOREIGN KEY FK_E1774A616BB74515');
        $this->addSql('ALTER TABLE scientist DROP FOREIGN KEY FK_E1774A61966F7FB6');
        $this->addSql('DROP TABLE cigarettes');
        $this->addSql('DROP TABLE drink');
        $this->addSql('DROP TABLE house');
        $this->addSql('DROP TABLE pet');
        $this->addSql('DROP TABLE scientist');
    }
}
