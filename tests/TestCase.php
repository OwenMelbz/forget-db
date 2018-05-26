<?php

namespace Tests;

use SQLite3;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Holds an application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    protected $dbDump = __DIR__ . '/../database.sql';

    protected $testDb = __DIR__. '/../database.sqlite';

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->app = $this->createApplication();
    }

    protected function setupTestDB()
    {
        @unlink($this->testDb);
        $db = new SQLite3($this->testDb);
        $query = file_get_contents($this->dbDump);
        $db->query($query);
    }

    protected function deleteTestDB()
    {
        @unlink($this->testDb);
    }

}
