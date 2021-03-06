<?php

declare(strict_types=1);


namespace Database\Seeders;


use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run() {
        Setting::firstOrCreate(['name' => 'pii'], ['value' => '36000-56000']);
        Setting::firstOrCreate(['name' => 'connection_request_username'], ['value' => 'ACS']);
        Setting::firstOrCreate(['name' => 'connection_request_password'], ['value' => 'ACS']);
        Setting::firstOrCreate(['name' => 'conversation_log'], ['value' => 'false']);
    }
}
