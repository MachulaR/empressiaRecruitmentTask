<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201009210236 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, hotel_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, beds INT NOT NULL, total_price INT NOT NULL, UNIQUE INDEX UNIQ_42C849553243BB18 (hotel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849553243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id)');
        $this->addSql('DROP TABLE reservations');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservations (id INT AUTO_INCREMENT NOT NULL, hotel_id_id INT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, beds INT NOT NULL, total_price INT DEFAULT NULL, UNIQUE INDEX UNIQ_4DA2399C905093 (hotel_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reservations ADD CONSTRAINT FK_4DA2399C905093 FOREIGN KEY (hotel_id_id) REFERENCES hotel (id)');
        $this->addSql('DROP TABLE reservation');
    }
}
