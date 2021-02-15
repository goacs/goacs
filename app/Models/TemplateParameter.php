<?php

declare(strict_types=1);


namespace App\Models;


use App\ACS\Entities\Flag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\TemplateParameter
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter query()
 * @mixin \Eloquent
 */
class TemplateParameter extends Model
{
    protected $table = 'templates_parameters';

    protected $fillable = ['template_id', 'name', 'value', 'type', 'flags'];


    protected $casts = [
        'flags' => Flag::class,
    ];

    public function template(): BelongsTo {
        return $this->belongsTo(Template::class);
    }
}
