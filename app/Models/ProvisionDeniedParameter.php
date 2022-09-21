<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvisionDeniedParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'parameter',
    ];

    protected $table = 'provision_denied_parameters';
}
