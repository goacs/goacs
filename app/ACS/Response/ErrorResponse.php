<?php

declare(strict_types=1);


namespace App\ACS\Response;


use App\ACS\Context;
use Symfony\Component\HttpFoundation\Response;

class ErrorResponse extends ACSResponse
{
    private string $message;

    public function __construct(Context $context, string $message)
    {
        parent::__construct($context, 'text');
        $this->message = $message;
        $this->context->response->setStatusCode(Response::HTTP_BAD_REQUEST);
        $this->context->response->header('Connection', 'close');
    }

    public function getBody(): string
    {
        return $this->message;
    }
}
