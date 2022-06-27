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
        'name', 'events', 'script'
    ];

    protected $with = ['rules', 'denied'];

    public function rules(): HasMany {
        return $this->hasMany(ProvisionRule::class);
    }

    public function denied(): HasMany {
        return $this->hasMany(ProvisionDeniedParameter::class);
    }
}
