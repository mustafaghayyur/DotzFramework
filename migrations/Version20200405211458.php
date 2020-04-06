<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200405211458 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'user module migration';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (
        					id INT AUTO_INCREMENT NOT NULL, 
        					email VARCHAR(120) DEFAULT \'email\' NOT NULL UNIQUE,
        					username VARCHAR(22) NOT NULL UNIQUE, 
        					password VARCHAR(255) NOT NULL, 
        					PRIMARY KEY(id)
        				);');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user;');
    }
}
