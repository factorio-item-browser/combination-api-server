<?php

// phpcs:ignoreFile

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210115131034 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Added some missing fields.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(<<<EOT
            ALTER TABLE Combination 
            ADD exportTime DATETIME DEFAULT NULL COMMENT 'The time when the combination was last exported.'
        EOT);
        $this->addSql(<<<EOT
            ALTER TABLE Job 
            ADD priority ENUM('admin', 'user', 'auto-update') NOT NULL COMMENT 'The priority of the export job.(DC2Type:job_priority)' AFTER combinationId
        EOT);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Combination DROP exportTime');
        $this->addSql('ALTER TABLE Job DROP priority');
    }
}
