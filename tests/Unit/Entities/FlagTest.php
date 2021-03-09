<?php

declare(strict_types=1);


namespace Tests\Unit\Entities;


use App\ACS\Entities\Flag;
use Tests\TestCase;

class FlagTest extends TestCase
{
    public function test_creating_flag_from_string() {
        $flag = Flag::fromString('RWSAX');
        $this->assertTrue($flag->read);
        $this->assertTrue($flag->write);
        $this->assertTrue($flag->send);
        $this->assertTrue($flag->object);
        $this->assertTrue($flag->system);

        $flag = Flag::fromString('RW');
        $this->assertTrue($flag->read);
        $this->assertTrue($flag->write);
        $this->assertFalse($flag->send);
        $this->assertFalse($flag->object);
        $this->assertFalse($flag->system);
    }

    public function test_creating_flag_from_array() {
        $data = [
            'read' => true,
            'write' => true,
        ];

        $flag = Flag::fromArray($data);
        $this->assertTrue($flag->read);
        $this->assertTrue($flag->write);
        $this->assertFalse($flag->send);
        $this->assertFalse($flag->object);
        $this->assertFalse($flag->system);
    }
}
