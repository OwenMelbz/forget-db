<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\UpdateService;

class UpdateTest extends TestCase
{
    public function test_i_should_not_get_offered_updates()
    {
        $updater = new UpdateService();
        $this->assertEmpty($updater->shouldUpdate());
    }

    public function test_i_can_check_for_updates()
    {
        $updater = new UpdateService();
        $update = $updater->searchForUpdates();
        $this->assertEmpty($update);
    }

    public function test_i_can_get_latest_releases()
    {
        $updater = new UpdateService();
        $update = $updater->getLatestRelease();
        $this->assertNotNull($update);
    }

    public function test_i_can_get_a_build()
    {
        $updater = new UpdateService();
        $update = $updater->getLatestRelease();
        $asset = $updater->getBuild($update['id']);
        $this->assertNotNull($asset);
    }

    public function test_i_can_get_the_right_file_to_overwrite()
    {
        $updater = new UpdateService();
        $path = $updater->fileToOverWrite();
        $this->assertEquals('forget-db.phar', $path);
    }

    public function test_the_updater_doesnt_crash()
    {
        $updater = new UpdateService();
        $update = $updater->searchForUpdates();
        $path = $updater->updateTo($update);

        $this->assertFileExists($path);
        @unlink($path);
        @unlink($path . '.backup');
    }
}
