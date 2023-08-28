<?php

declare(strict_types=1);


namespace App\ACS\Entities;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class ParameterValueStruct implements Arrayable
{
    public string $name = "";
    public string $value = '';
    public string $type = '';
    public Flag $flag;

    public function __construct()
    {
        $this->flag = new Flag();
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function setFlag(Flag $flag): self {
        $this->flag = $flag;
        return $this;
    }

    public function toArray(): array {
        return [
          'name' => $this->name,
          'value' => $this->value,
          'type' => $this->type,
          'flag' => $this->flag->toArray()
        ];
    }

    public static function fromArray(array $data): static {
        if(!isset($data['value'])) {
            $data['value'] = '';
        }

        if(!isset($data['flag'])) {
            $data['flag'] = 'R';
        }

        $obj = new static();
        $obj->name = $data['name'];
        $obj->value = $data['value'];
        $obj->type = $data['type'] ?? 'xsd:string';

        if(is_array($data['flag'])) {
            $obj->flag = Flag::fromArray($data['flag']);
        } elseif(is_string($data['flag'])) {
            $obj->flag = Flag::fromString($data['flag']);
        } else if ($data['flag'] instanceof Flag){
            $obj->flag = $data['flag'];
        }

        return $obj;
    }

    public static function isInstancePath(string $path) {
        $chunks = explode('.', $path);
        $chunksCount = count($chunks);

        if(isset($chunks[$chunksCount - 2]) && is_numeric($chunks[$chunksCount - 2])) {
            return true;
        }

        return false;
    }
}
