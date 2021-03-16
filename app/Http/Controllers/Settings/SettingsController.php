<?php

declare(strict_types=1);


namespace App\Http\Controllers\Settings;


use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\SettingStoreRequest;
use App\Models\Setting;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingsController extends Controller
{
    public function index() {
        return new JsonResource($this->flatSettings());
    }

    public function store(SettingStoreRequest $request) {
        foreach($request->validated() as $key => $value) {
            Setting::setValue($key, $value);
        }

        return new JsonResource($this->flatSettings());
    }

    private function flatSettings() {
        $settings = Setting::all();
        return $settings->mapWithKeys(fn($item) => [$item->name => $item->value]);
    }
}
