<?php

declare(strict_types=1);


namespace App\ACS\Entities;


class Event
{
    private string $code = "";
    public string $key = "";
    public int $codeNumber = -1;

    public function getCode(): string {
        return $this->code;
    }

    public function setCode(string $code) {
        $this->code = $code;
        $this->codeNumber = $this->extractNumberFromCode($code);
    }

    public function getCodeNumber(): int {
        return $this->codeNumber;
    }

    public function extractNumberFromCode(string $code): int {
        $chunks = explode(' ', $code);
        if(count($chunks) > 1 && is_numeric($chunks[0])) {
            return (int) $chunks[0];
        }

        return -1;
    }
}
