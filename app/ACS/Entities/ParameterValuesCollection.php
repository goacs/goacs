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

    public function assignDefaultFlags(Collection $parameterInfoCollection) {
        /**
         * @var string $key
         * @var ParameterValueStruct $item
         */
        foreach ($this->items as $key => $item) {
            /** @var ParameterInfoStruct $parameterInfo */
            if($parameterInfo = $parameterInfoCollection->has($item->name)) {
                $flag = new Flag();
                if($parameterInfo->writable && Str::endsWith($parameterInfo->name,'.')) {
                    $flag->object = true;
                    $flag->write = true;
                } else if ($parameterInfo->writable) {
                    $flag->write = true;
                }
                $this->items[$key]->flag = $flag;
            }
        }
    }
}
