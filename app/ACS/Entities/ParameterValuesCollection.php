<?php

declare(strict_types=1);


namespace App\ACS\Entities;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ParameterValuesCollection extends Collection
{
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

    public function filterByFlag(string $flag): ParameterValuesCollection {
        return $this->filter(fn(ParameterValueStruct $parameterValueStruct) => $parameterValueStruct->flag->{$flag} === true);
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

                    if(Str::endsWith($item->name,'.')) {
                        $flag->object = true;
                        $this->items[$key]->type = 'object';
                    }
                }
                $this->items[$key]->flag = $flag;
            }
        }
    }

    public function diff($items)
    {
        $diff = new ParameterValuesCollection();
        /** @var ParameterValueStruct $item */
        foreach($this->items as $item) {
            $exist = false;
            /** @var ParameterValueStruct $othItem */
            foreach ($items as $othItem) {
                if($item->name === $othItem->name && $item->value !== $othItem->value) {
                    $exist = true;
                    $diff->put($item->name, $item);
                }
            }

            if($exist === false) {
                $diff->put($item->name, $item);
            }
        }

        return $diff;
    }
}
