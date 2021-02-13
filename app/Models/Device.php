<?php

declare(strict_types=1);


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

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
        'connection_request_url', 'connection_request_user', 'connection_request_password'];
}
