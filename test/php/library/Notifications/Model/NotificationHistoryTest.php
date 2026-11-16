<?php

// SPDX-FileCopyrightText: 2026 Icinga GmbH <https://icinga.com>
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Tests\Icinga\Module\Notifications\Model;

use Icinga\Module\Notifications\Model\NotificationHistory;
use ipl\Sql\Connection;
use PHPUnit\Framework\TestCase;

class NotificationHistoryTest extends TestCase
{
    public function testObjectSourcesCanBeAggregatedFromNotificationHistory(): void
    {
        // Query assembly does not connect to the database. The source subquery
        // traverses the inverse object -> notification_history relation.
        foreach (['pgsql', 'mysql'] as $adapter) {
            $db = new Connection(['db' => $adapter, 'dbname' => 'notifications']);
            $query = NotificationHistory::on($db)
                ->with('object')
                ->withColumns(['object.sources']);

            [$sql] = $query->dump();

            $this->assertStringContainsString('notification_history', $sql);
            $this->assertStringContainsString('object_source', $sql);
        }
    }
}
