<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260320140328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE character_party (character_id INTEGER NOT NULL, party_id INTEGER NOT NULL, PRIMARY KEY (character_id, party_id), CONSTRAINT FK_7756A9821136BE75 FOREIGN KEY (character_id) REFERENCES character (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7756A982213C1059 FOREIGN KEY (party_id) REFERENCES party (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7756A9821136BE75 ON character_party (character_id)');
        $this->addSql('CREATE INDEX IDX_7756A982213C1059 ON character_party (party_id)');
        $this->addSql('CREATE TABLE character_class_skill (character_class_id INTEGER NOT NULL, skill_id INTEGER NOT NULL, PRIMARY KEY (character_class_id, skill_id), CONSTRAINT FK_BC806FEDB201E281 FOREIGN KEY (character_class_id) REFERENCES character_class (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BC806FED5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BC806FEDB201E281 ON character_class_skill (character_class_id)');
        $this->addSql('CREATE INDEX IDX_BC806FED5585C142 ON character_class_skill (skill_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__character AS SELECT id, name, level, strength, dexterity, constitution, intelligence, wisdom, charisma, health_points, image FROM character');
        $this->addSql('DROP TABLE character');
        $this->addSql('CREATE TABLE character (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, level INTEGER NOT NULL, strength INTEGER NOT NULL, dexterity INTEGER NOT NULL, constitution INTEGER NOT NULL, intelligence INTEGER NOT NULL, wisdom INTEGER NOT NULL, charisma INTEGER NOT NULL, health_points INTEGER NOT NULL, image VARCHAR(255) DEFAULT NULL, user_id INTEGER NOT NULL, race_id INTEGER NOT NULL, character_class_id INTEGER NOT NULL, CONSTRAINT FK_937AB034A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_937AB0346E59D40D FOREIGN KEY (race_id) REFERENCES race (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_937AB034B201E281 FOREIGN KEY (character_class_id) REFERENCES character_class (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO character (id, name, level, strength, dexterity, constitution, intelligence, wisdom, charisma, health_points, image) SELECT id, name, level, strength, dexterity, constitution, intelligence, wisdom, charisma, health_points, image FROM __temp__character');
        $this->addSql('DROP TABLE __temp__character');
        $this->addSql('CREATE INDEX IDX_937AB034A76ED395 ON character (user_id)');
        $this->addSql('CREATE INDEX IDX_937AB0346E59D40D ON character (race_id)');
        $this->addSql('CREATE INDEX IDX_937AB034B201E281 ON character (character_class_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__party AS SELECT id, name, description, max_size FROM party');
        $this->addSql('DROP TABLE party');
        $this->addSql('CREATE TABLE party (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, max_size INTEGER DEFAULT NULL, creator_id INTEGER NOT NULL, CONSTRAINT FK_89954EE061220EA6 FOREIGN KEY (creator_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO party (id, name, description, max_size) SELECT id, name, description, max_size FROM __temp__party');
        $this->addSql('DROP TABLE __temp__party');
        $this->addSql('CREATE INDEX IDX_89954EE061220EA6 ON party (creator_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE character_party');
        $this->addSql('DROP TABLE character_class_skill');
        $this->addSql('CREATE TEMPORARY TABLE __temp__character AS SELECT id, name, level, strength, dexterity, constitution, intelligence, wisdom, charisma, health_points, image FROM character');
        $this->addSql('DROP TABLE character');
        $this->addSql('CREATE TABLE character (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, level INTEGER NOT NULL, strength INTEGER NOT NULL, dexterity INTEGER NOT NULL, constitution INTEGER NOT NULL, intelligence INTEGER NOT NULL, wisdom INTEGER NOT NULL, charisma INTEGER NOT NULL, health_points INTEGER NOT NULL, image VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO character (id, name, level, strength, dexterity, constitution, intelligence, wisdom, charisma, health_points, image) SELECT id, name, level, strength, dexterity, constitution, intelligence, wisdom, charisma, health_points, image FROM __temp__character');
        $this->addSql('DROP TABLE __temp__character');
        $this->addSql('CREATE TEMPORARY TABLE __temp__party AS SELECT id, name, description, max_size FROM party');
        $this->addSql('DROP TABLE party');
        $this->addSql('CREATE TABLE party (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, max_size INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO party (id, name, description, max_size) SELECT id, name, description, max_size FROM __temp__party');
        $this->addSql('DROP TABLE __temp__party');
    }
}
