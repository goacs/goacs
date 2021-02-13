<?php

declare(strict_types=1);


namespace App\ACS\Entities;


use Illuminate\Support\Str;

class ParameterInfoStruct
{
    public string $name = "";
    public bool $writable;

    public bool $object;

    public function setName($name) {
        $this->name = $name;
        $this->object = $this->isObject();
    }

    public function isObject() {
        return Str::endsWith('.', $this->name) && $this->writable;
    }
}
