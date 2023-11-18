<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231118102405 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE website_posts_authors (post_id BIGINT NOT NULL, trombinoscope_person_id BIGINT NOT NULL, PRIMARY KEY(post_id, trombinoscope_person_id))');
        $this->addSql('CREATE INDEX IDX_F4C11054B89032C ON website_posts_authors (post_id)');
        $this->addSql('CREATE INDEX IDX_F4C1105C69EE0FA ON website_posts_authors (trombinoscope_person_id)');
        $this->addSql('ALTER TABLE website_posts_authors ADD CONSTRAINT FK_F4C11054B89032C FOREIGN KEY (post_id) REFERENCES website_posts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_posts_authors ADD CONSTRAINT FK_F4C1105C69EE0FA FOREIGN KEY (trombinoscope_person_id) REFERENCES website_trombinoscope_persons (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE website_posts DROP author');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website_posts_authors DROP CONSTRAINT FK_F4C11054B89032C');
        $this->addSql('ALTER TABLE website_posts_authors DROP CONSTRAINT FK_F4C1105C69EE0FA');
        $this->addSql('DROP TABLE website_posts_authors');
        $this->addSql('ALTER TABLE website_posts ADD author VARCHAR(100) DEFAULT NULL');
    }
}
