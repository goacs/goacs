<?php


namespace App\Models;


use App\ACS\Response\FaultResponse;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Fault
 *
 * @property int $id
 * @property int $device_id
 * @property string $full_xml
 * @property string $code
 * @property string $message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Fault newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fault newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fault query()
 * @method static \Illuminate\Database\Eloquent\Builder|Fault whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fault whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fault whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fault whereFullXml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fault whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fault whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fault whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property array $detail
 * @method static \Illuminate\Database\Eloquent\Builder|Fault whereDetail($value)
 */
class Fault extends Model
{
    protected $table = 'faults';

    protected $casts = [
        'detail' => 'array'
    ];

    public static function fromFaultResponse(Device $device, FaultResponse $faultResponse): self {
        $fault = new static();
        $fault->device_id = $device->id;
        $fault->full_xml = $faultResponse->fullXML;
        $fault->code = $faultResponse->faultCode;
        $fault->message = $faultResponse->faultString;
        $fault->detail = json_decode(json_encode($faultResponse->detail), true);
        $fault->save();
        return $fault;
    }
}
