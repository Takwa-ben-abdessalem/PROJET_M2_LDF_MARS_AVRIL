<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260624000200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Store authenticated user cookie preferences';
    }

    public function up(Schema $schema): void
    {
        $preferences = $schema->createTable('cookie_preference');
        $preferences->addColumn('id', 'integer', ['autoincrement' => true]);
        $preferences->addColumn('user_id', 'integer');
        $preferences->addColumn('necessary', 'boolean');
        $preferences->addColumn('statistics', 'boolean');
        $preferences->addColumn('marketing', 'boolean');
        $preferences->addColumn('created_at', 'datetime_immutable');
        $preferences->addColumn('updated_at', 'datetime_immutable');
        $preferences->setPrimaryKey(['id']);
        $preferences->addUniqueIndex(['user_id'], 'uniq_cookie_preference_user');
        $preferences->addForeignKeyConstraint('users', ['user_id'], ['id'], ['onDelete' => 'CASCADE']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('cookie_preference');
    }
}
