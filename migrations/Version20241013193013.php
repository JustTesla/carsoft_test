<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241013193013 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ecu_software (id UUID NOT NULL, ecu_id UUID NOT NULL, version VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AD6B5A93F2887E5B ON ecu_software (ecu_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AD6B5A93F2887E5BBF1CD3C3 ON ecu_software (ecu_id, version)');
        $this->addSql('COMMENT ON COLUMN ecu_software.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN ecu_software.ecu_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE ecu_software_service (id UUID NOT NULL, ecu_software_id UUID NOT NULL, service_id UUID NOT NULL, replacement TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_59FBDAFA320CE3A ON ecu_software_service (ecu_software_id)');
        $this->addSql('CREATE INDEX IDX_59FBDAFAED5CA9E6 ON ecu_software_service (service_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_59FBDAFA320CE3AED5CA9E6 ON ecu_software_service (ecu_software_id, service_id)');
        $this->addSql('COMMENT ON COLUMN ecu_software_service.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN ecu_software_service.ecu_software_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN ecu_software_service.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN ecu_software_service.replacement IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE ecu_software ADD CONSTRAINT FK_AD6B5A93F2887E5B FOREIGN KEY (ecu_id) REFERENCES ecu (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecu_software_service ADD CONSTRAINT FK_59FBDAFA320CE3A FOREIGN KEY (ecu_software_id) REFERENCES ecu_software (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecu_software_service ADD CONSTRAINT FK_59FBDAFAED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('INSERT INTO ecu_software (SELECT gen_random_uuid() as id, ecu.id as ecu_id, \'*\' as version from ecu)');
        $this->addSql('INSERT INTO ecu_software_service (SELECT ecu_service.id as id, ecu_software.id as ecu_software_id, ecu_service.service_id as service_id, ecu_service.replacement as replacement from ecu_service left join ecu_software on ecu_software.ecu_id = ecu_service.ecu_id and ecu_software.version = \'*\')');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ecu_software DROP CONSTRAINT FK_AD6B5A93F2887E5B');
        $this->addSql('ALTER TABLE ecu_software_service DROP CONSTRAINT FK_59FBDAFA320CE3A');
        $this->addSql('ALTER TABLE ecu_software_service DROP CONSTRAINT FK_59FBDAFAED5CA9E6');
        $this->addSql('DROP TABLE ecu_software');
        $this->addSql('DROP TABLE ecu_software_service');
    }
}
