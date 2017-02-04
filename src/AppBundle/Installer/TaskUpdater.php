<?php

declare(strict_types=1);

namespace AppBundle\Installer;

use Goat\Bundle\Installer\Updater;
use Goat\Core\Client\ConnectionInterface;
use Goat\Core\Transaction\Transaction;

/**
 * Self installer.
 */
class TaskUpdater extends Updater
{
    /**
     * {@inheritdoc}
     */
    public function installSchema(ConnectionInterface $connection, Transaction $transaction)
    {
        $connection->query(<<<EOT
CREATE TABLE task (
    id SERIAL PRIMARY KEY,
    id_account INTEGER DEFAULT NULL,
    is_done BOOLEAN DEFAULT FALSE,
    is_starred BOOLEAN DEFAULT FALSE,
    is_hidden BOOLEAN DEFAULT FALSE,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL DEFAULT '',
    priority INTEGER NOT NULL DEFAULT 0,
    ts_added TIMESTAMP NOT NULL DEFAULT NOW(),
    ts_updated TIMESTAMP NOT NULL DEFAULT NOW(),
    ts_deadline TIMESTAMP DEFAULT NULL,
    ts_done TIMESTAMP DEFAULT NULL,
    ts_unhide TIMESTAMP DEFAULT NULL,
    FOREIGN KEY (id_account) REFERENCES account (id) ON DELETE SET NULL
);
EOT
        );

        $connection->query(<<<EOT
CREATE TABLE task_comment (
    id SERIAL PRIMARY KEY,
    id_task INTEGER NOT NULL,
    id_account INTEGER DEFAULT NULL,
    description TEXT NOT NULL DEFAULT '',
    ts_added TIMESTAMP NOT NULL DEFAULT NOW(),
    ts_updated TIMESTAMP NOT NULL DEFAULT NOW(),
    FOREIGN KEY (id_task) REFERENCES task (id) ON DELETE CASCADE,
    FOREIGN KEY (id_account) REFERENCES account (id) ON DELETE SET NULL
);
EOT
        );

        $connection->query(<<<EOT
CREATE TABLE task_history (
    id SERIAL PRIMARY KEY,
    id_task INTEGER NOT NULL,
    id_account INTEGER DEFAULT NULL,
    id_comment INTEGER DEFAULT NULL,
    action VARCHAR(64) NOT NULL DEFAULT 'update',
    ts_updated TIMESTAMP NOT NULL DEFAULT NOW(),
    FOREIGN KEY (id_task) REFERENCES task (id) ON DELETE CASCADE,
    FOREIGN KEY (id_account) REFERENCES account (id) ON DELETE SET NULL,
    FOREIGN KEY (id_comment) REFERENCES task_comment (id) ON DELETE SET NULL
);
EOT
        );

                $connection->query(<<<EOT
CREATE INDEX task_account_done_deadline_idx ON task (id_account, is_done, ts_deadline);
EOT

        );
        $connection->query(<<<EOT
CREATE TABLE task_tag (
    id SERIAL PRIMARY KEY,
    id_account INTEGER DEFAULT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_account) REFERENCES account (id) ON DELETE SET NULL,
    UNIQUE (id_account, name)
);
EOT
        );
        $connection->query(<<<EOT
CREATE TABLE task_tag_map (
    id_tag INTEGER NOT NULL,
    id_task INTEGER NOT NULL,
    FOREIGN KEY (id_tag) REFERENCES task (id) ON DELETE CASCADE,
    FOREIGN KEY (id_task) REFERENCES task_tag (id) ON DELETE CASCADE
);
EOT
        );
    }
}
