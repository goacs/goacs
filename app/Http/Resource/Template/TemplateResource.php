<?php

declare(strict_types=1);


namespace App\Http\Resource\Template;


use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parameters_count' => $this->parameters()->count(),
            'devices_count' => $this->devices()->count(),
        ];
    }
}
