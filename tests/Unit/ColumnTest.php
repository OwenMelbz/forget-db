<?php

namespace Tests\Unit;

use App\Column;
use Tests\TestCase;

class ColumnTest extends TestCase
{
    public function test_i_can_generate_a_replacement_string()
    {
        $column = new Column('my_column', 'email');

        $replacement = $column->generate();

        $this->assertContains('@', $replacement);
    }

    public function test_i_can_get_the_column_name()
    {
        $column = new Column('my_column', 'email');

        $name = $column->getName();

        $this->assertEquals('my_column', $name);
    }

    public function test_i_can_get_a_unique_column()
    {
        $column = new Column('my_column', 'unique:email');
        $name = $column->getName();

        $this->assertEquals('my_column', $name);
    }
}
