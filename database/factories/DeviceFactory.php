<?php


namespace Database\Factories;


use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{

    protected $model = Device::class;

    public function definition()
    {
        return [
            'serial_number' => 'TEST'.$this->faker->randomNumber(5),
            'oui' => '001122',
            'software_version' => '1.2.1',
            'hardware_version' => '1.0.0',
            'connection_request_url' => 'http://127.0.0.1',
            'connection_request_user' => 'ACS',
            'connection_request_password' => 'ACS',
        ];
    }
}
