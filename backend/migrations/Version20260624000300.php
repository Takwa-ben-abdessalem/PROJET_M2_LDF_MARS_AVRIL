<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260624000300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align generated Doctrine index names and MariaDB column definitions';
    }

    public function up(Schema $schema): void
    {
        if (!$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform) {
            return;
        }

        $this->addSql('CREATE INDEX IDX_30113729A76ED395 ON consent_log (user_id)');
        $this->addSql('DROP INDEX idx_consent_log_user ON consent_log');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7876C4DDA ON event (organizer_id)');
        $this->addSql('DROP INDEX idx_event_organizer ON event');
        $this->addSql('CREATE INDEX IDX_62A8A7A7A76ED395 ON registration (user_id)');
        $this->addSql('DROP INDEX idx_registration_user ON registration');
        $this->addSql('CREATE INDEX IDX_62A8A7A771F7E88B ON registration (event_id)');
        $this->addSql('DROP INDEX idx_registration_event ON registration');
        $this->addSql('ALTER TABLE users CHANGE phone phone VARCHAR(30) DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE last_activity_at last_activity_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        if (!$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform) {
            return;
        }

        $this->addSql('CREATE INDEX idx_consent_log_user ON consent_log (user_id)');
        $this->addSql('DROP INDEX IDX_30113729A76ED395 ON consent_log');
        $this->addSql('CREATE INDEX idx_event_organizer ON event (organizer_id)');
        $this->addSql('DROP INDEX IDX_3BAE0AA7876C4DDA ON event');
        $this->addSql('CREATE INDEX idx_registration_user ON registration (user_id)');
        $this->addSql('DROP INDEX IDX_62A8A7A7A76ED395 ON registration');
        $this->addSql('CREATE INDEX idx_registration_event ON registration (event_id)');
        $this->addSql('DROP INDEX IDX_62A8A7A771F7E88B ON registration');
    }
}
