<?php

declare(strict_types=1);


namespace App\ACS\XML;


class BoolConverter
{
    public static function stringToBool(string $strBool): bool {
        if($strBool === "0" || strtolower($strBool) === "false") {
            return false;
        }

        return true;
    }
}
