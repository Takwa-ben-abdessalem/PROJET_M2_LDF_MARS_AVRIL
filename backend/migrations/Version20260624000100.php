<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260624000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create EventFlow MVP tables';
    }

    public function up(Schema $schema): void
    {
        $users = $schema->createTable('users');
        $users->addColumn('id', 'integer', ['autoincrement' => true]);
        $users->addColumn('email', 'string', ['length' => 180]);
        $users->addColumn('password', 'string', ['length' => 255]);
        $users->addColumn('first_name', 'string', ['length' => 100]);
        $users->addColumn('last_name', 'string', ['length' => 100]);
        $users->addColumn('phone', 'string', ['length' => 30, 'notnull' => false]);
        $users->addColumn('roles', 'json');
        $users->addColumn('consent_date', 'datetime_immutable');
        $users->addColumn('consent_version', 'string', ['length' => 20]);
        $users->addColumn('is_anonymized', 'boolean');
        $users->addColumn('created_at', 'datetime_immutable');
        $users->addColumn('last_activity_at', 'datetime_immutable', ['notnull' => false]);
        $users->setPrimaryKey(['id']);
        $users->addUniqueIndex(['email'], 'uniq_user_email');

        $events = $schema->createTable('event');
        $events->addColumn('id', 'integer', ['autoincrement' => true]);
        $events->addColumn('organizer_id', 'integer');
        $events->addColumn('title', 'string', ['length' => 180]);
        $events->addColumn('description', 'text');
        $events->addColumn('event_date', 'datetime_immutable');
        $events->addColumn('location', 'string', ['length' => 180]);
        $events->addColumn('max_participants', 'integer');
        $events->addColumn('is_published', 'boolean');
        $events->addColumn('created_at', 'datetime_immutable');
        $events->setPrimaryKey(['id']);
        $events->addIndex(['organizer_id'], 'idx_event_organizer');
        $events->addForeignKeyConstraint('users', ['organizer_id'], ['id']);

        $registrations = $schema->createTable('registration');
        $registrations->addColumn('id', 'integer', ['autoincrement' => true]);
        $registrations->addColumn('user_id', 'integer');
        $registrations->addColumn('event_id', 'integer');
        $registrations->addColumn('registered_at', 'datetime_immutable');
        $registrations->addColumn('status', 'string', ['length' => 20]);
        $registrations->setPrimaryKey(['id']);
        $registrations->addIndex(['user_id'], 'idx_registration_user');
        $registrations->addIndex(['event_id'], 'idx_registration_event');
        $registrations->addUniqueIndex(['user_id', 'event_id'], 'uniq_registration_user_event');
        $registrations->addForeignKeyConstraint('users', ['user_id'], ['id']);
        $registrations->addForeignKeyConstraint('event', ['event_id'], ['id']);

        $logs = $schema->createTable('consent_log');
        $logs->addColumn('id', 'integer', ['autoincrement' => true]);
        $logs->addColumn('user_id', 'integer', ['notnull' => false]);
        $logs->addColumn('action', 'string', ['length' => 40]);
        $logs->addColumn('timestamp', 'datetime_immutable');
        $logs->addColumn('ip_address', 'string', ['length' => 64]);
        $logs->addColumn('details', 'text');
        $logs->setPrimaryKey(['id']);
        $logs->addIndex(['user_id'], 'idx_consent_log_user');
        $logs->addForeignKeyConstraint('users', ['user_id'], ['id'], ['onDelete' => 'SET NULL']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('consent_log');
        $schema->dropTable('registration');
        $schema->dropTable('event');
        $schema->dropTable('users');
    }
}
