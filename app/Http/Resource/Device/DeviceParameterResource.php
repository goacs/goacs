<?php


namespace App\Http\Resource\Device;


use Illuminate\Http\Resources\Json\JsonResource;

class DeviceParameterResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'device_id' => $this->device_id,
            'name' => $this->name,
            'value' => $this->value,
            'type' => $this->type,
            'flags' => $this->flags->toArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
