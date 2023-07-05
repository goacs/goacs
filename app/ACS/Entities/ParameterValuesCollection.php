<?php

declare(strict_types=1);


namespace App\ACS\Entities;


use App\Models\DeviceParameter;
use App\Models\ParameterInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ParameterValuesCollection extends Collection
{
    public function getValue(string $path): string {
        return (string) $this->firstWhere('name', '=', $path)?->value ?? '';
    }

    public function parameterExists(string $path): bool {
        return $this->firstWhere('name', $path) !== null;
    }

    public function filterByChunkCount(int $minCount = 1, int $maxCount = 99) {
        return $this->filter(function (ParameterValueStruct $item) use ($minCount, $maxCount) {
            $count = count(array_filter(explode('.', $item->name)));
            return $count >= $minCount && $count <= $maxCount;
        });
    }

    public function filterEndsWithDot(): ParameterValuesCollection {
        return $this->filter(function(ParameterValueStruct $item) {
            return Str::endsWith($item->name, '.');
        });
    }

    public function filterByFlag(string $flag, bool $condition = true): ParameterValuesCollection {
        return $this->filter(fn(ParameterValueStruct $parameterValueStruct) => $parameterValueStruct->flag->{$flag} === $condition);
    }

    public function filterCanInstance(): ParameterValuesCollection {
        return $this->filter(fn(ParameterValueStruct $parameterValueStruct) => ParameterValueStruct::isInstancePath($parameterValueStruct->name) === false);
    }

    public function filterInstances(): ParameterValuesCollection {
        return $this->filter(fn(ParameterValueStruct $parameterValueStruct) => ParameterValueStruct::isInstancePath($parameterValueStruct->name) === true);
    }


    public function assignDefaultFlags(Collection $parameterInfoCollection) {
        /**
         * @var string $key
         * @var ParameterValueStruct $item
         */
        foreach ($this->items as $key => $item) {
            /** @var ParameterInfoStruct $parameterInfo */
            if($parameterInfo = $parameterInfoCollection->get($item->name)) {
                $flag = new Flag();
                if($parameterInfo->writable) {
                    $flag->write = true;
                }

                if(Str::endsWith($item->name,'.') && $flag->write === true) {
                    $flag->object = true;
                    $this->items[$key]->type = 'object';
                }

                $this->items[$key]->flag = $flag;
            }
        }
    }

    public function diff($items, bool $skipSend = false)
    {
        $diff = new ParameterValuesCollection();
        /** @var ParameterValueStruct $item */
        foreach($this->items as $item) {
            $exist = false;
            /** @var ParameterValueStruct $othItem */
            foreach ($items as $othItem) {
                if($item->name === $othItem->name) {
                    $exist = true;

                    if($skipSend && ($item->flag->send || $othItem->flag->send)) {
                        continue;
                    }

                    if($item->value !== $othItem->value || $item->type !== $othItem->type || $item->flag->toString() !== $othItem->flag->toString()) {
                        $diff->put($item->name, $item);
                    }
                }
            }

            if($exist === false) {
                $diff->put($item->name, $item);
            }
        }

        return $diff;
    }

    public static function fromEloquent(Collection $collection) {
        $parameterCollection = new ParameterValuesCollection();
        $collection->each(function(ParameterInterface $parameter) use ($parameterCollection) {
            $parameterCollection->put($parameter->name, $parameter->toParamaterValueStruct());
        });

        return $parameterCollection;
    }

    public static function fromArray(array $data): static {
        $collection = new static();
        foreach($data as $param) {
            if(!isset($param['name']) && isset($param['path'])) {
                $param['name'] = $param['path'];
            }
            $collection->put($param['name'], ParameterValueStruct::fromArray($param));
        }

        return $collection;
    }

}
