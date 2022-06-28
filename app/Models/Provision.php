<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin \Eloquent
 */
class Provision extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'events', 'requests', 'script'
    ];

    protected $with = ['rules', 'denied'];

    public function rules(): HasMany {
        return $this->hasMany(ProvisionRule::class);
    }

    public function denied(): HasMany {
        return $this->hasMany(ProvisionDeniedParameter::class);
    }

    public function eventsArray(): array {
        return explode(',', $this->events);
    }

    public function requestsArray(): array {
        return explode(',', $this->requests);
    }
}
