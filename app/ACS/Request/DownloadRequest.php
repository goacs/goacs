<?php

declare(strict_types=1);


namespace App\ACS\Request;


use App\ACS\Context;

class DownloadRequest extends ACSRequest
{
  private string $fileType;
  private string $fileUrl;

  public function __construct(Context $context, string $fileType, string $fileUrl)
  {
    parent::__construct($context);

    $this->fileType = $fileType;
    $this->fileUrl = $fileUrl;
  }

  public function getBody(): string
    {
      $body = '<cwmp:Download>';
      $body .= '<FileType>'.$this->fileType.'</FileType>';
      $body .= '<URL>'.$this->fileUrl.'</URL>';
        //Later add Username Password FileSize fields

	    $body .= '</cwmp:Download>';

	    return $this->withBaseBody($body);
    }
}
