<?php


namespace Tests\Unit\Entities;


use App\ACS\Entities\ParameterValueStruct;
use Tests\TestCase;

class ParameterValueStructTest extends TestCase
{
    public function test_is_instance_path() {
        $path = "A.B.C.D.";
        $path2  = "A.B.C.D.1.";

        $this->assertFalse(ParameterValueStruct::isInstancePath($path));
        $this->assertTrue(ParameterValueStruct::isInstancePath($path2));
    }
}
