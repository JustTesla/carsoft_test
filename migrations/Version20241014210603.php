<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241014210603 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ecu_service DROP CONSTRAINT fk_3a1716b9f2887e5b');
        $this->addSql('ALTER TABLE ecu_service DROP CONSTRAINT fk_3a1716b9ed5ca9e6');
        $this->addSql('DROP TABLE ecu_service');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ecu_service (id UUID NOT NULL, ecu_id UUID DEFAULT NULL, service_id UUID DEFAULT NULL, replacement TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_3a1716b9f2887e5bed5ca9e6 ON ecu_service (ecu_id, service_id)');
        $this->addSql('CREATE INDEX idx_3a1716b9ed5ca9e6 ON ecu_service (service_id)');
        $this->addSql('CREATE INDEX idx_3a1716b9f2887e5b ON ecu_service (ecu_id)');
        $this->addSql('COMMENT ON COLUMN ecu_service.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN ecu_service.ecu_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN ecu_service.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN ecu_service.replacement IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE ecu_service ADD CONSTRAINT fk_3a1716b9f2887e5b FOREIGN KEY (ecu_id) REFERENCES ecu (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecu_service ADD CONSTRAINT fk_3a1716b9ed5ca9e6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('INSERT INTO ecu_service (SELECT ecu_software_service.id as id, ecu_software.ecu_id as ecu_id, ecu_software_service.service_id as service_id, ecu_software_service.replacement as replacement FROM ecu_software_service LEFT JOIN ecu_software ON ecu_software_service.ecu_software_id = ecu_software.id)');
    }
}
