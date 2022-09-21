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
 * @property boolean $debug
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
 * @method static \Illuminate\Database\Eloquent\Builder|Device whereDebug($value)
 * @property-read Collection|\App\Models\Log[] $faults
 * @property-read int|null $faults_count
 * @method static Builder|Device createdAfter($date)
 * @method static Builder|Device updatedLast24Hours()
 * @method static Builder|Device whereProductClass($value)
 */
class Device extends Model
{
    use HasFactory;

    protected $table = 'device';

    protected $fillable = ['serial_number', 'oui', 'software_version', 'hardware_version',
        'connection_request_url', 'connection_request_user', 'connection_request_password',
        'updated_at', 'product_class', 'model_name', 'debug'];

    protected $casts = [
        'debug' => 'boolean'
    ];

    /**
     * @return HasMany|DeviceParameter
     */
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
        return $this->hasMany(Log::class);
    }

    public function scopeCreatedAfter(Builder $query, $date) {
        if(! $date instanceof Carbon) {
            $date = new Carbon($date);
        }

        return $query->where('created_at', '>=', $date->addMinutes(2));
    }

    public function getTemplatesParameters() {
        $templatesWithParameters = $this
            ->templates()
            ->orderByPivot('priority')
            ->with('parameters')
            ->get();

        return $templatesWithParameters->pluck('parameters')->flatten(1);
    }

    public function scopeUpdatedLast24Hours(Builder $query) {
        return $query->where('updated_at', '>=', now()->subDay());
    }
}
