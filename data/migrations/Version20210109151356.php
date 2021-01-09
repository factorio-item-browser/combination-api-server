<?php

// phpcs:ignoreFile

declare(strict_types=1);

namespace FactorioItemBrowser\CombinationApi\Server\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210109151356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'The initial setup of the database.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql(<<<EOT
            CREATE TABLE Combination (
                id BINARY(16) NOT NULL COMMENT 'The id of the combination.(DC2Type:uuid_binary)', 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_bin` ENGINE = InnoDB COMMENT = 'The table holding the combinations.'
        EOT);
        $this->addSql(<<<EOT
            CREATE TABLE CombinationXMod (
                combinationId BINARY(16) NOT NULL COMMENT 'The id of the combination.(DC2Type:uuid_binary)', 
                modId BINARY(16) NOT NULL COMMENT 'The internal id of the mod.(DC2Type:uuid_binary)', 
                INDEX IDX_C3D0611AFE40C4A7 (combinationId), 
                INDEX IDX_C3D0611AE07F9145 (modId), 
                PRIMARY KEY(combinationId, modId)
            ) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
        EOT);
        $this->addSql(<<<EOT
            CREATE TABLE Job (
                id BINARY(16) NOT NULL COMMENT 'The id of the job.(DC2Type:uuid_binary)', 
                combinationId BINARY(16) DEFAULT NULL COMMENT 'The id of the combination.(DC2Type:uuid_binary)', 
                status ENUM('queued', 'downloading', 'processing', 'uploading', 'uploaded', 'importing','done', 'error') NOT NULL COMMENT 'The current status of the export job.(DC2Type:job_status)', 
                errorMessage LONGTEXT NOT NULL COMMENT 'The error message in case the job failed.', 
                INDEX IDX_C395A618FE40C4A7 (combinationId), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_bin` ENGINE = InnoDB COMMENT = 'The table holding the export jobs.'
        EOT);
        $this->addSql(<<<EOT
            CREATE TABLE JobChange (
                id BINARY(16) NOT NULL COMMENT 'The internal id of the job change.(DC2Type:uuid_binary)', 
                jobId BINARY(16) DEFAULT NULL COMMENT 'The id of the job.(DC2Type:uuid_binary)', 
                initiator VARCHAR(255) NOT NULL COMMENT 'The initiator of the change.', 
                status ENUM('queued', 'downloading', 'processing', 'uploading', 'uploaded', 'importing', 'done', 'error') NOT NULL COMMENT 'The new status of the export job.(DC2Type:job_status)', 
                timestamp DATETIME NOT NULL COMMENT 'The time of the change.', 
                INDEX IDX_4E6E285956D231E7 (jobId), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_bin` ENGINE = InnoDB COMMENT = 'The table holding the changes of the export jobs.'
        EOT);
        $this->addSql(<<<EOT
            CREATE TABLE `Mod` (
                id BINARY(16) NOT NULL COMMENT 'The internal id of the mod.(DC2Type:uuid_binary)',
                name VARCHAR(255) NOT NULL COMMENT 'The name of the mod.',
                UNIQUE INDEX UNIQ_2FB915A85E237E06 (name),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_bin` ENGINE = InnoDB COMMENT = 'The table holding the mods.'
        EOT);
        $this->addSql(<<<EOT
            ALTER TABLE
                CombinationXMod
            ADD
                CONSTRAINT FK_C3D0611AFE40C4A7 FOREIGN KEY (combinationId) REFERENCES Combination (id)
        EOT);
        $this->addSql(<<<EOT
            ALTER TABLE
                CombinationXMod
            ADD
                CONSTRAINT FK_C3D0611AE07F9145 FOREIGN KEY (modId) REFERENCES `Mod` (id)
        EOT);
        $this->addSql(<<<EOT
            ALTER TABLE 
                Job 
            ADD 
                CONSTRAINT FK_C395A618FE40C4A7 FOREIGN KEY (combinationId) REFERENCES Combination (id)
        EOT);
        $this->addSql(<<<EOT
            ALTER TABLE 
                JobChange 
            ADD 
                CONSTRAINT FK_4E6E285956D231E7 FOREIGN KEY (jobId) REFERENCES Job (id)
        EOT);
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE CombinationXMod DROP FOREIGN KEY FK_C3D0611AFE40C4A7');
        $this->addSql('ALTER TABLE Job DROP FOREIGN KEY FK_C395A618FE40C4A7');
        $this->addSql('ALTER TABLE JobChange DROP FOREIGN KEY FK_4E6E285956D231E7');
        $this->addSql('ALTER TABLE CombinationXMod DROP FOREIGN KEY FK_C3D0611AE07F9145');
        $this->addSql('DROP TABLE Combination');
        $this->addSql('DROP TABLE CombinationXMod');
        $this->addSql('DROP TABLE Job');
        $this->addSql('DROP TABLE JobChange');
        $this->addSql('DROP TABLE `Mod`');
    }
}
