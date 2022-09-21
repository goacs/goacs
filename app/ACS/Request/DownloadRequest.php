<?php

declare(strict_types=1);


namespace App\ACS\Request;


use App\ACS\Context;

class DownloadRequest extends ACSRequest
{
    private string $fileType;
    private string $fileUrl;
    private int $filesize;

    public function __construct(Context $context, string $fileType, string $fileUrl, int $filesize)
    {
        parent::__construct($context);

        $this->fileType = $fileType;
        $this->fileUrl = $fileUrl;
        $this->filesize = $filesize;
    }

    public function getBody(): string
    {
        $envelopeId = $this->context->envelopeId();
        $body = '<cwmp:Download>';
        $body .= '<CommandKey>'.$envelopeId.'</CommandKey>';
        $body .= '<FileType>'.$this->fileType.'</FileType>';
        $body .= '<URL>'.$this->fileUrl.'</URL>';
        $body .= '<Username></Username>';
        $body .= '<Password></Password>';
        $body .= '<FileSize>'.$this->filesize.'</FileSize>';
        $body .= '<TargetFilename></TargetFilename>';
        $body .= '<DelaySeconds>0</DelaySeconds>';
        $body .= '<SuccessURL></SuccessURL>';
        $body .= '<FailureURL></FailureURL>';
        $body .= '</cwmp:Download>';

        return $this->withBaseBody($body);
    }
}
