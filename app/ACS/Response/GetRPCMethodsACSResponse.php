<?php

declare(strict_types=1);


namespace App\ACS\Response;


class GetRPCMethodsACSResponse extends ACSResponse
{

    public function getBody(): string
    {
        $body = '<cwmp:GetRPCMethodsResponse><MethodList soap-enc:arrayType="xsd:string[3]">';
        $body .= '<string>Inform</string>';
        $body .= '<string>GetRPCMethods</string>';
        $body .= '<string>TransferComplete</string>';
        $body .= '</MethodList></cwmp:GetRPCMethodsResponse>';

        return $this->withBaseBody($body);
    }
}
