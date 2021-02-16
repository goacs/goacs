<?php

declare(strict_types=1);


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * App\Models\Device
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Device newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Device newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Device query()
 * @mixin \Eloquent
 */
class Device extends Model
{
    protected $table = 'device';

    protected $fillable = ['serial_number', 'oui', 'software_version', 'hardware_version',
        'connection_request_url', 'connection_request_user', 'connection_request_password',
        'updated_at'];

    public function parameters(): HasMany {
        return $this->hasMany(DeviceParameter::class);
    }

    public function templates(): BelongsToMany {
        return $this->belongsToMany(Template::class, 'device_to_template')->withPivot('priority');
    }

    public function tasks(): MorphMany {
        return $this->morphMany(Task::class, 'for');
    }
}
