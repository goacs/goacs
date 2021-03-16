<?php

declare(strict_types=1);


namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
 * @property int $id
 * @property string $serial_number
 * @property string $oui
 * @property string|null $software_version
 * @property string|null $hardware_version
 * @property string $product_class
 * @property string $connection_request_url
 * @property string|null $connection_request_user
 * @property string|null $connection_request_password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DeviceParameter[] $parameters
 * @property-read int|null $parameters_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read int|null $tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Template[] $templates
 * @property-read int|null $templates_count
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereConnectionRequestPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereConnectionRequestUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereConnectionRequestUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereHardwareVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereOui($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereSoftwareVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereUpdatedAt($value)
 */
class Device extends Model
{
    use HasFactory;

    protected $table = 'device';

    protected $fillable = ['serial_number', 'oui', 'software_version', 'hardware_version',
        'connection_request_url', 'connection_request_user', 'connection_request_password',
        'updated_at', 'product_class'];

    public function parameters(): HasMany {
        return $this->hasMany(DeviceParameter::class);
    }

    public function templates(): BelongsToMany {
        return $this->belongsToMany(Template::class, 'device_to_template')->withPivot('priority');
    }

    public function tasks(): MorphMany {
        return $this->morphMany(Task::class, 'for');
    }

    public function faults(): HasMany {
        return $this->hasMany(Fault::class);
    }

    public function scopeCreatedAfter(Builder $query, $date) {
        return $query->where('created_at', '>=', $date);
    }


    public function getTemplatesParameters() {
        $templatesWithParameters = $this
            ->templates()
            ->orderByPivot('priority')
            ->with('parameters')
            ->get();

        return $templatesWithParameters->pluck('parameters')->flatten(1);
    }
}
