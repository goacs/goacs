<?php

declare(strict_types=1);


namespace App\ACS\Request;


class DownloadRequest extends ACSRequest
{
    public function getBody(): string
    {
        $body = '<cwmp:Download>';
        $body .= '<FileType></FileType>';
		$body .= '<URL></URL>';
        //Later add Username Password FileSize fields

	    $body .= '</cwmp:Download>';

	    return $this->withBaseBody($body);
    }
}
