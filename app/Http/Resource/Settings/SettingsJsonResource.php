<?php


namespace App\Http\Resource\Settings;


use Illuminate\Http\Resources\Json\JsonResource;

class SettingsJsonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'read_behaviour' => $this->resource['read_behaviour'],
            'pii' => (string) $this->resource['pii'],
            'lookup_cache_ttl' => (int) $this->resource['lookup_cache_ttl'],
            'connection_request_username' => (string) $this->resource['connection_request_username'],
            'connection_request_password' => (string) $this->resource['connection_request_password'],
            'conversation_log' => boolval($this->resource['conversation_log']),
            'mappings' => (array) $this->resource['mappings'],
            'webhook_ssl_verify' => (bool) $this->resource['webhook_ssl_verify'],
            'webhook_timeout' => $this->resource['webhook_timeout'],
            'webhook_after_provision' => $this->resource['webhook_after_provision'],
        ];
    }
}
