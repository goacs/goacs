<?php

declare(strict_types=1);


namespace Model;


use App\Models\Setting;
use Tests\TestCase;

class SettingTest extends TestCase
{
    public function test_decode_json() {
        $this->assertEquals("1", Setting::decodeValue("1"));
        $this->assertEquals("test", Setting::decodeValue("test"));
        $this->assertEquals(false, Setting::decodeValue("false"));
        $this->assertEquals(true, Setting::decodeValue("true"));
        $this->assertEquals(null, Setting::decodeValue("null"));
        $this->assertEquals("3000", Setting::decodeValue("3000"));
        $this->assertEquals("3000-4000", Setting::decodeValue("3000-4000"));
        $this->assertEquals(["test" => "test123"], Setting::decodeValue('{"test":"test123"}'));
    }
}
