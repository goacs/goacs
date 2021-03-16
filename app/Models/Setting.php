<?php

declare(strict_types=1);


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting
 * @package App\Models
 * @mixin \Eloquent
 */
class Setting extends Model
{
    protected $table = 'settings';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'name';

    protected $fillable = ['name', 'value'];

    public static function setValue(string $key, $value) {
        static::updateOrCreate(['name' => $key],['value' => $value]);
    }

    public static function getValue(string $key): mixed {
        $data = static::where('name', $key)->first();
        if($data !== null) {
            return $data->value;
        }

        return '';
    }
}
