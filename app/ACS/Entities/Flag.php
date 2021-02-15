<?php

declare(strict_types=1);


namespace App\ACS\Entities;


use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Flag implements CastsAttributes
{
    public function __construct(
        public bool $read = true,
        public bool $write = false,
        public bool $object = false,
        public bool $send = false,
        public bool $system = false,
    )
    {}

    public static function fromString(string $flag): Flag {
        $flag = strtoupper($flag);

        $flagObject = new Flag();

        for($i = 0; $i < strlen($flag); $i++) {
            switch ($flag[$i]) {
                case 'R':
                    $flagObject->read = true;
                    break;
                case 'W':
                    $flagObject->write = true;
                    break;
                case 'A':
                    $flagObject->object = true;
                    break;
                case 'S':
                    $flagObject->send = true;
                    break;
                case 'X':
                    $flagObject->system = true;
                    break;
            }
        }

        return $flagObject;
    }

    public static function fromArray(array $data): Flag {
        return new Flag(
            $data['read'],
            $data['write'],
            $data['object'],
            $data['send'],
            $data['system'],
        );
    }

    public function toArray(): array {
        return [
            'read' => $this->read,
            'write' => $this->write,
            'object' => $this->object,
            'send' => $this->send,
            'system' => $this->system,
        ];
    }

    public function toJson(): string {
        return json_encode($this->toArray());
    }

    public function get($model, string $key, $value, array $attributes)
    {
        return Flag::fromArray(json_decode($value, true));
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $value->toJson();
    }
}
