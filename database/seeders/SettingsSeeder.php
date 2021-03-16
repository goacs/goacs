<?php

declare(strict_types=1);


namespace Database\Seeders;


use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run() {
        Setting::setValue('pii', '36000-56000');
        Setting::setValue('connection_request_username', 'ACS');
        Setting::setValue('connection_request_password', 'ACS');
    }
}
