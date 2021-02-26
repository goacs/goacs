<?php


namespace App\Models;


use App\ACS\Entities\ParameterValueStruct;

interface ParameterInterface
{
    public function toParamaterValueStruct(): ParameterValueStruct;
}
