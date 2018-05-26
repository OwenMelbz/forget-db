<?php

namespace Tests\Unit;

use App\Condition;
use Tests\TestCase;

class ConditionTest extends TestCase
{
    public function test_i_can_get_raw_condition()
    {
        $condition = new Condition('where user_id = 1');

        $this->assertEquals('where user_id = 1', $condition->getRaw());
    }

    public function test_i_can_get_a_query_from_conditions()
    {
        $condition = new Condition('where user_id = 1');
        $this->assertEquals('user_id = 1', $condition->getWhere());

        $condition = new Condition('Where user_id = 1');
        $this->assertEquals('user_id = 1', $condition->getWhere());

        $condition = new Condition('WHERE user_id = 1');
        $this->assertEquals('user_id = 1', $condition->getWhere());

        $condition = new Condition('or user_id = 1');
        $this->assertEquals('user_id = 1', $condition->getWhere());

        $condition = new Condition('Or user_id = 1');
        $this->assertEquals('user_id = 1', $condition->getWhere());

        $condition = new Condition('OR user_id = 1');
        $this->assertEquals('user_id = 1', $condition->getWhere());

        $condition = new Condition('|| user_id = 1');
        $this->assertEquals('user_id = 1', $condition->getWhere());

        $condition = new Condition('and user_id = 1');
        $this->assertEquals('user_id = 1', $condition->getWhere());

        $condition = new Condition('And user_id = 1');
        $this->assertEquals('user_id = 1', $condition->getWhere());

        $condition = new Condition('AND user_id = 1');
        $this->assertEquals('user_id = 1', $condition->getWhere());

        $condition = new Condition('&& user_id = 1');
        $this->assertEquals('user_id = 1', $condition->getWhere());
    }
}
