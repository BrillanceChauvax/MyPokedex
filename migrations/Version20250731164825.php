<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250731164825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pokemon_talent (id INT AUTO_INCREMENT NOT NULL, pokemon_id INT DEFAULT NULL, talent_id INT DEFAULT NULL, is_hidden TINYINT(1) DEFAULT NULL, INDEX IDX_7A307EA12FE71C3E (pokemon_id), INDEX IDX_7A307EA118777CEF (talent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE talent (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pokemon_talent ADD CONSTRAINT FK_7A307EA12FE71C3E FOREIGN KEY (pokemon_id) REFERENCES pokemon (id)');
        $this->addSql('ALTER TABLE pokemon_talent ADD CONSTRAINT FK_7A307EA118777CEF FOREIGN KEY (talent_id) REFERENCES talent (id)');
        $this->addSql('ALTER TABLE pokemon ADD taille DOUBLE PRECISION DEFAULT NULL, ADD poids DOUBLE PRECISION DEFAULT NULL, ADD cri_url VARCHAR(255) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD pv INT DEFAULT NULL, ADD attaque INT DEFAULT NULL, ADD attaque_spe INT DEFAULT NULL, ADD defense INT DEFAULT NULL, ADD defense_spe INT DEFAULT NULL, ADD vitesse INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pokemon_talent DROP FOREIGN KEY FK_7A307EA12FE71C3E');
        $this->addSql('ALTER TABLE pokemon_talent DROP FOREIGN KEY FK_7A307EA118777CEF');
        $this->addSql('DROP TABLE pokemon_talent');
        $this->addSql('DROP TABLE talent');
        $this->addSql('ALTER TABLE pokemon DROP taille, DROP poids, DROP cri_url, DROP description, DROP pv, DROP attaque, DROP attaque_spe, DROP defense, DROP defense_spe, DROP vitesse');
    }
}
