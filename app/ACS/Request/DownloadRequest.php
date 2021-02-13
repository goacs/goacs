<?php

declare(strict_types=1);


namespace App\ACS\Request;


class DownloadRequest extends ACSRequest
{
    public function getBody(): string
    {
        return "";
    }
}
