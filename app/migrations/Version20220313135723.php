<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220313135723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'RC-1-Add-User';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                 CREATE TABLE user (
                     id INT UNSIGNED AUTO_INCREMENT NOT NULL, 
                     email VARCHAR(255) NOT NULL, 
                     roles LONGTEXT NOT NULL COMMENT '(DC2Type:json)', 
                     password VARCHAR(255) NOT NULL,
                     is_enabled TINYINT(1) DEFAULT 0 NOT NULL, 
                     created_at DATETIME DEFAULT UTC_TIMESTAMP NOT NULL, 
                     updated_at DATETIME DEFAULT UTC_TIMESTAMP NOT NULL, 
                     UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), 
                     PRIMARY KEY(id)                   
                ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB;
                SQL
        );
    }
}
