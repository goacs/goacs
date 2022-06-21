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

    public function rules(): HasMany {
        return $this->hasMany(ProvisionRule::class);
    }

    public function deniedParameters(): HasMany {
        return $this->hasMany(ProvisionDeniedParameter::class);
    }
}
