<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230730113705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file CHANGE filename filename VARCHAR(255) NOT NULL, CHANGE size size INT NOT NULL, CHANGE description description VARCHAR(255) NOT NULL, CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE video ADD file VARCHAR(255) NOT NULL, CHANGE format format VARCHAR(255) NOT NULL, CHANGE duration duration INT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file CHANGE filename filename VARCHAR(255) DEFAULT NULL, CHANGE size size INT DEFAULT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE type type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE video DROP file, CHANGE format format VARCHAR(255) DEFAULT NULL, CHANGE duration duration INT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL');
    }
}
