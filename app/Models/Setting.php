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
    const CACHE_PREFIX = 'SETTINGS_';

    protected $table = 'settings';

    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'name';

    protected $fillable = ['name', 'value'];

    public static function setValue(string $key, $value) {
        static::updateOrCreate(['name' => $key],['value' => (string) $value]);
        \Cache::forget(self::CACHE_PREFIX.$key);
    }

    public static function getValue(string $key): mixed {
        return \Cache::remember(self::CACHE_PREFIX.$key, 3600, function() use ($key) {
            $data = static::where('name', $key)->first();
            if($data !== null) {
                return $data->value;
            }

            return '';
        });
    }
}
