<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121130161112 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql("ALTER TABLE contest ADD styles LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)'");
        $this->addSql('UPDATE contest SET styles = ? WHERE LOCATE(?, theme)', array($this->convertValue('array', array('overlay' => 'dark')), 'dark'));
        $this->addSql('UPDATE contest SET styles = ? WHERE LOCATE(?, theme)', array($this->convertValue('array', array('overlay' => 'light')), 'light'));
        $this->addSql("ALTER TABLE contest DROP theme");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql("ALTER TABLE contest ADD theme VARCHAR(255) NOT NULL, DROP styles");
        $this->addSql("UPDATE contest SET theme = ?", array('theme-light'));
    }

    private function convertValue($type, $value)
    {
        return Type::getType($type)->convertToDatabaseValue($value, $this->connection->getDatabasePlatform());
    }
}
