<?php

declare(strict_types=1);


namespace App\Models;


use App\ACS\Entities\Flag;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\XML\XSDTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\DeviceParameter
 *
 * @property int $id
 * @property int $device_id
 * @property string $name
 * @property string $value
 * @property string $type
 * @property string|Flag $flags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereFlags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeviceParameter whereValue($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Device $device
 * @method static Builder|DeviceParameter pathset(string $path)
 */
class DeviceParameter extends Model implements ParameterInterface
{
    protected $table = 'device_parameters';

    protected $fillable = ['device_id', 'name', 'value', 'type', 'flags'];

    protected $casts = [
        'flags' => Flag::class,
    ];

    public function device(): BelongsTo {
        return $this->belongsTo(Device::class);
    }

    public static function setParameter(int $device_id, string $path, $value, string $flags = 'RWS', $type = null) {
        $parameter = static::where(['name' => $path, 'device_id' => $device_id])->firstOrNew();
        if($parameter->exists == false) {
            //ParameterNotExist
            if($type === null) {
                $type = XSDTypes::STRING;
            }
        } else {
            $type = $parameter->type;
        }

        $parameter->device_id = $device_id;
        $parameter->name = $path;
        $parameter->value = (string)$value;
        $parameter->type = $type;
        $parameter->flags = Flag::fromString($flags);
        $parameter->save();
    }

    public static function getParameterValue(int $device_id, string $path) {
        $parameter = static::where(['name' => $path, 'device_id' => $device_id])->first();

        //magic XD
        return $parameter?->value ?? '';
    }

    public function scopePathset(Builder $query, string $path) {
        $query->where('name', 'like', $path.'%');
    }

    public function toParamaterValueStruct(): ParameterValueStruct {
        $obj = new ParameterValueStruct();
        $obj->name = $this->name;
        $obj->type = $this->type;
        $obj->value = $this->value;
        $obj->flag = $this->flags;
        return $obj;
    }

    public static function massUpdateOrInsert(Device $device, ParameterValuesCollection $parameterValuesCollection) {
        $currentDBParameters = ParameterValuesCollection::fromEloquent($device->parameters()->get());
        $diffParameters = $parameterValuesCollection->diff($currentDBParameters);
        $diffParameters2 = $currentDBParameters->diff($parameterValuesCollection);
//        dump("Diff resp->db: ".$diffParameters->count());
//        dump("Diff db->resp: ".$diffParameters2->count());

        foreach($diffParameters->chunk(300) as $chunk) {

            $values = collect();
            /** @var ParameterValueStruct $item */
            foreach ($chunk as $item) {
                $data = collect([
                    'device_id' => $device->id,
                    'name' => $item->name,
                    'value' => $item->value,
                    'type' => $item->type,
                    'flags' => $item->flag->toJson(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $data = $data->map(fn($item) => "'{$item}'");
                $values[] = '('.$data->join(',').')';
            }
            $values = $values->join(',');

            $query = "INSERT INTO device_parameters(device_id, name, value, type, flags, created_at, updated_at) VALUES {$values}
                    ON DUPLICATE KEY UPDATE name=values(name), value=values(value), type=values(type), flags=values(flags), updated_at=values(updated_at)";

            \DB::statement($query);
        }
    }
}
