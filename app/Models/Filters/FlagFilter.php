<?php

declare(strict_types=1);


namespace App\Models\Filters;


use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class FlagFilter implements Filter
{

    public function __invoke(Builder $query, $value, string $property)
    {
        if(is_array($value)) {
            $value = implode(',', $value);
        }

        $jsonArray = json_decode($value);

        foreach($jsonArray as $key => $value) {
            $query->where($property.'->'.$key, $value);
        }
    }
}
