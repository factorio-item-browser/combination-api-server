<?php

// phpcs:ignoreFile

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210206083352 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Added creationTime to Job.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(<<<EOT
            ALTER TABLE Job 
            ADD creationTime DATETIME NOT NULL COMMENT 'The creation time of the job.' AFTER status
        EOT);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE Job DROP creationTime');
    }
}
