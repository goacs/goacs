<?php


namespace App\Http\Resource\Settings;


use Illuminate\Http\Resources\Json\JsonResource;

class SettingsJsonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'pii' => (string) $this->resource['pii'],
            'connection_request_username' => (string) $this->resource['connection_request_username'],
            'connection_request_password' => (string) $this->resource['connection_request_password'],
            'conversation_log' => boolval($this->resource['conversation_log']),
            'mappings' => (array) $this->resource['mappings']
        ];
    }
}
