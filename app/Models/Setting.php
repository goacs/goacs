<?php

declare(strict_types=1);


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Setting
 *
 * @package App\Models
 * @mixin \Eloquent
 * @property string $name
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereValue($value)
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
        static::updateOrCreate(['name' => $key],['value' => (string) self::encodeValue($value)]);
        \Cache::forget(self::CACHE_PREFIX.$key);
    }

    public static function getValue(string $key): mixed {
        return \Cache::remember(self::CACHE_PREFIX.$key, 3600, function() use ($key) {
            $data = static::where('name', $key)->first();
            if($data !== null) {
                return static::decodeValue($data->value);
            }

            return '';
        });
    }

    public static function decodeValue(string $value): mixed {
        try {
            $value = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {

        }

        return $value;
    }

    public static function encodeValue($value) {
        if(is_array($value)) {
            return json_encode($value);
        }

        return $value;
    }
}
