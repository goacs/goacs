<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvisionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'parameter', 'operator', 'value'
    ];

    protected $table = 'provision_rules';
}
