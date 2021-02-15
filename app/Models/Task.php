<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Task extends Model
{
    protected $table = 'tasks';

    public function morph(): MorphTo {
        return $this->morphTo('for');
    }
}
