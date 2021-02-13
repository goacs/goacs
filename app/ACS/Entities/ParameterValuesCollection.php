<?php

declare(strict_types=1);


namespace App\ACS\Entities;


use Illuminate\Support\Collection;

class ParameterValuesCollection extends Collection
{
    public function filterByChunkCount(int $minCount = 1, int $maxCount = 99) {
        return $this->filter(function (ParameterValueStruct $item) use ($minCount, $maxCount) {
            $count = count(array_filter(explode('.', $item->name)));
            return $count >= $minCount && $count <= $maxCount;
        });
    }
}
