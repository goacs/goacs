<?php


namespace App\Http\Resource\Device;


use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'serial_number' => $this->serial_number,
            'product_class' => $this->product_class,
            'model_name' => $this->model_name,
            'oui' => $this->oui,
            'software_version' => $this->software_version,
            'hardware_version' => $this->hardware_version,
            'connection_request_url' => $this->connection_request_url,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'debug' => (bool) $this->debug,
        ];
    }
}
