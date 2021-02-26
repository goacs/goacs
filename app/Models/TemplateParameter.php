<?php

declare(strict_types=1);


namespace App\Models;


use App\ACS\Entities\Flag;
use App\ACS\Entities\ParameterValueStruct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\TemplateParameter
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $template_id
 * @property string $name
 * @property string $value
 * @property string $type
 * @property $flags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Template $template
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter whereFlags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TemplateParameter whereValue($value)
 */
class TemplateParameter extends Model implements ParameterInterface
{
    protected $table = 'templates_parameters';

    protected $fillable = ['template_id', 'name', 'value', 'type', 'flags'];


    protected $casts = [
        'flags' => Flag::class,
    ];

    public function template(): BelongsTo {
        return $this->belongsTo(Template::class);
    }

    public function toParamaterValueStruct(): ParameterValueStruct {
        $obj = new ParameterValueStruct();
        $obj->name = $this->name;
        $obj->type = $this->type;
        $obj->value = $this->value;
        $obj->flag = $this->flags;
        return $obj;
    }
}
