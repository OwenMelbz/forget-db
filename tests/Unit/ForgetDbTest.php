<?php

namespace Tests\Unit;

use App\Commands\ForgetMeNow;
use App\Services\DatabaseService;
use App\Services\ForgetDbService;
use App\Services\UtilityService;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class ForgetDbTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setupTestDB();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->deleteTestDB();
    }

    public function test_i_can_use_the_forget_service()
    {
        $config = Yaml::parse(UtilityService::stubConfig());

        (new DatabaseService([
            'driver' => 'sqlite',
            'database' => $this->testDb,
        ]))->testConnection();

        $service = new ForgetDbService($config);

        $command = $this->createMock(ForgetMeNow::class);

        $service->forget($command);

        $this->assertCount(3, $service->getTables());
    }
}
