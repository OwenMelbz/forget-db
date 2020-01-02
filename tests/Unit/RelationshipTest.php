<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Relationship;
use DMS\PHPUnitExtensions\ArraySubset\Assert;

class RelationshipTest extends TestCase
{
    public function test_i_can_spin_up_a_relationship()
    {
        $relationship = new Relationship('users on user_id = address_id');

        $this->assertInstanceOf(Relationship::class, $relationship);
    }

    public function test_i_can_get_raw()
    {
        $relationship = new Relationship('users on user_id = address_id');

        $this->assertEquals('users on user_id = address_id', $relationship->getRaw());
    }

    public function test_i_can_get_type()
    {
        $this->assertEquals(
            'inner',
            (new Relationship('users on user_id = address_id'))->getType()
        );

        $this->assertEquals(
            'left',
            (new Relationship('left:users on user_id = address_id'))->getType()
        );

        $this->assertEquals(
            'right',
            (new Relationship('right:users on user_id = address_id'))->getType()
        );
    }

    public function test_i_can_get_join_params()
    {
        $params = (new Relationship('users on user_id = address_id'))->getJoin();

        $this->assertNotEmpty($params);
        $this->assertCount(4, $params);
        Assert::assertArraySubset([
            'users', 'user_id', '=', 'address_id'
        ], $params);
    }
}
