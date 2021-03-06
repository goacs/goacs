<?php

declare(strict_types=1);


namespace App\ACS\Request;


use App\ACS\Context;

class DeleteObjectRequest extends ACSRequest
{
    private string $path;
    private string $key;

    public function __construct(Context $context, string $path, string $key = '')
    {
        parent::__construct($context);
        $this->path = $path;
        $this->key = $key;
    }

    public function getBody(): string
    {
        $body = '<cwmp:DeleteObject>';
        $body .= '<ObjectName>'.$this->path.'</ObjectName>';
        $body .= '<ParameterKey>'.$this->key.'</ParameterKey>';
        $body .= '</cwmp:DeleteObject>';

        return $this->withBaseBody($body);
    }
}
