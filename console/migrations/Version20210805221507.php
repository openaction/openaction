<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210805221507 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE integrations_telegram_apps_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE integrations_telegram_apps_authorizations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE integrations_telegram_apps (id BIGINT NOT NULL, organization_id BIGINT NOT NULL, bot_username VARCHAR(50) NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CE0EFEB2D17F50A6 ON integrations_telegram_apps (uuid)');
        $this->addSql('CREATE INDEX IDX_CE0EFEB232C8A3DE ON integrations_telegram_apps (organization_id)');
        $this->addSql('COMMENT ON COLUMN integrations_telegram_apps.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE integrations_telegram_apps_authorizations (id BIGINT NOT NULL, app_id BIGINT NOT NULL, member_id BIGINT NOT NULL, api_token VARCHAR(80) NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3CEAEE84D17F50A6 ON integrations_telegram_apps_authorizations (uuid)');
        $this->addSql('CREATE INDEX IDX_3CEAEE847987212D ON integrations_telegram_apps_authorizations (app_id)');
        $this->addSql('CREATE INDEX IDX_3CEAEE847597D3FE ON integrations_telegram_apps_authorizations (member_id)');
        $this->addSql('COMMENT ON COLUMN integrations_telegram_apps_authorizations.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE integrations_telegram_apps ADD CONSTRAINT FK_CE0EFEB232C8A3DE FOREIGN KEY (organization_id) REFERENCES organizations (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE integrations_telegram_apps_authorizations ADD CONSTRAINT FK_3CEAEE847987212D FOREIGN KEY (app_id) REFERENCES integrations_telegram_apps (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE integrations_telegram_apps_authorizations ADD CONSTRAINT FK_3CEAEE847597D3FE FOREIGN KEY (member_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE integrations_telegram_apps_authorizations DROP CONSTRAINT FK_3CEAEE847987212D');
        $this->addSql('DROP SEQUENCE integrations_telegram_apps_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE integrations_telegram_apps_authorizations_id_seq CASCADE');
        $this->addSql('DROP TABLE integrations_telegram_apps');
        $this->addSql('DROP TABLE integrations_telegram_apps_authorizations');
    }
}
