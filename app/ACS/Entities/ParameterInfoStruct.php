<?php

declare(strict_types=1);


namespace App\ACS\Entities;


use Illuminate\Support\Str;

class ParameterInfoStruct
{
    public string $name = "";
    public bool $writable = false;

    public bool $object = false;

    public function setName($name): self {
        $this->name = $name;
        $this->object = $this->isObject();
        return $this;
    }

    public function isObject() {
        return Str::endsWith( $this->name, '.') && $this->writable;
    }
}
