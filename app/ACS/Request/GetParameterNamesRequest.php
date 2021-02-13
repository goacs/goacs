<?php

declare(strict_types=1);


namespace App\ACS\Request;


use App\ACS\Context;

class GetParameterNamesRequest extends ACSRequest
{
    private string $path;
    /**
     * @var bool
     */
    private bool $nextlevel;

    public function __construct(Context $context, string $path, bool $nextlevel = false)
    {
        parent::__construct($context);
        $this->path = $path;
        $this->nextlevel = $nextlevel;
    }

    public function getBody(): string
    {
        return $this->withBaseBody("<cwmp:GetParameterNames>
			<ParameterPath>{$this->path}</ParameterPath>
			<NextLevel>".var_export($this->nextlevel, true)."</NextLevel>
		</cwmp:GetParameterNames>");
    }
}
