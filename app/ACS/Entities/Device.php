<?php

declare(strict_types=1);


namespace App\ACS\Entities;


class Device
{
    public string $oui = '';
    public string $manufacturer = '';
    public string $serialNumber = '';
    public string $productClass = '';
    public string $modelName = '';
    public bool $new = false;
    public string $root = '';

}
