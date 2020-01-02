<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\UtilityService;
use Symfony\Component\Yaml\Yaml;
use App\Services\ForgetDbService;
use App\Services\DatabaseService;
use App\Commands\ForgetMeNowCommand;

class ForgetDbTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->setupTestDB();
    }

    public function tearDown(): void
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

        $command = $this->createMock(ForgetMeNowCommand::class);

        $service->forget($command);

        $this->assertCount(3, $service->getTables());
    }
}
