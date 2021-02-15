<?php

declare(strict_types=1);


namespace App\ACS\Entities;


use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ParameterInfoCollection extends Collection
{
    public function filterByChunkCount(int $minCount = 1, int $maxCount = 99): ParameterInfoCollection {
        return $this->filter(function (ParameterInfoStruct $item) use ($minCount, $maxCount) {
            $count = count(array_filter(explode('.', $item->name)));
            return $count >= $minCount && $count <= $maxCount;
        });
    }

    public function filterEndsWithDot(): ParameterInfoCollection {
        return $this->filter(function(ParameterInfoStruct $item) {
            return Str::endsWith($item->name, '.');
        });
    }

    public function toParameterValuesCollecton(): ParameterValuesCollection {
        $collection = new ParameterValuesCollection();
        /** @var ParameterInfoStruct $item */
        foreach($this->items as $item) {
            $parameterValueStruct = new ParameterValueStruct();
            $parameterValueStruct->name = $item->name;
            $parameterValueStruct->flag = new Flag();
            if($item->writable) {
                $parameterValueStruct->flag->write = true;
            }
            if(Str::endsWith($item->name,'.')) {
                $parameterValueStruct->flag->object = true;
                $parameterValueStruct->type = 'object';
            }
            $collection->put($item->name, $parameterValueStruct);
        }

        return $collection;
    }
}
