<?php

declare(strict_types=1);


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DeviceParameter
 *
 * @property int $id
 * @property int $device_id
 * @property string $name
 * @property string $value
 * @property string $type
 * @property string $flags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereFlags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereValue($value)
 * @mixin \Eloquent
 */
class DeviceParameter extends Model
{
    protected $table = 'device_parameters';

    protected $fillable = ['device_id', 'name', 'value', 'type', 'flags'];


}
