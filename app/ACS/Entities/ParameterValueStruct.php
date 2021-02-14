<?php

declare(strict_types=1);


namespace App\ACS\Entities;


class ParameterValueStruct
{
    public string $name = "";
    public string $value;
    public string $type;
    public Flag $flag;

    public function __construct()
    {
        $this->flag = new Flag();
    }

    public function setName(string $name) {
        $this->name = $name;
    }
}
