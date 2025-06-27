<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240616002747 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ecu (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN ecu.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE ecu_service (id UUID NOT NULL, ecu_id UUID DEFAULT NULL, service_id UUID DEFAULT NULL, replacement TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3A1716B9F2887E5B ON ecu_service (ecu_id)');
        $this->addSql('CREATE INDEX IDX_3A1716B9ED5CA9E6 ON ecu_service (service_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3A1716B9F2887E5BED5CA9E6 ON ecu_service (ecu_id, service_id)');
        $this->addSql('COMMENT ON COLUMN ecu_service.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN ecu_service.ecu_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN ecu_service.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN ecu_service.replacement IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE service (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN service.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE ecu_service ADD CONSTRAINT FK_3A1716B9F2887E5B FOREIGN KEY (ecu_id) REFERENCES ecu (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ecu_service ADD CONSTRAINT FK_3A1716B9ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ecu_service DROP CONSTRAINT FK_3A1716B9F2887E5B');
        $this->addSql('ALTER TABLE ecu_service DROP CONSTRAINT FK_3A1716B9ED5CA9E6');
        $this->addSql('DROP TABLE ecu');
        $this->addSql('DROP TABLE ecu_service');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
