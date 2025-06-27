<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241015080003 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DFE26D0E5E237E06 ON ecu (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E19D9AD25E237E06 ON service (name)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_E19D9AD25E237E06');
        $this->addSql('DROP INDEX UNIQ_DFE26D0E5E237E06');
    }
}
