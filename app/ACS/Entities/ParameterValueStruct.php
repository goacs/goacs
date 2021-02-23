<?php

declare(strict_types=1);


namespace App\ACS\Entities;


class ParameterValueStruct
{
    public string $name = "";
    public string $value = '';
    public string $type = '';
    public Flag $flag;

    public function __construct()
    {
        $this->flag = new Flag();
    }

    public function setName(string $name) {
        $this->name = $name;
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
