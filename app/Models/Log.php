<?php


namespace App\Models;


use App\ACS\Context;
use App\ACS\Response\FaultResponse;
use App\ACS\Types;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Log
 *
 * @property int $id
 * @property int $device_id
 * @property string $full_xml
 * @property string $code
 * @property string $message
 * @property string $type
 * @property string $from
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log query()
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereFullXml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property array $detail
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereDetail($value)
 * @property-read \App\Models\Device $device
 * @method static Builder|Log createdAfter($date)
 * @method static Builder|Log fault()
 * @method static Builder|Log last24Hours()
 * @method static Builder|Log type(string $type)
 * @method static Builder|Log whereFrom($value)
 * @method static Builder|Log whereType($value)
 */
class Log extends Model
{
    use MassPrunable;

    protected $table = 'logs';

    protected $fillable = ['from', 'type', 'full_xml', 'code', 'message', 'detail'];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    protected $casts = [
        'detail' => 'array'
    ];

    public function device(): BelongsTo {
        return $this->belongsTo(Device::class);
    }

    public static function fromFaultResponse(Device $device, FaultResponse $faultResponse): self {
        $log = new static();
        $log->device_id = $device->id;
        $log->full_xml = $faultResponse->fullXML;
        $log->code = $faultResponse->faultCode;
        $log->message = $faultResponse->faultString;
        $log->detail = json_decode(json_encode($faultResponse->detail), true);
        $log->type = Types::FaultResponse;
        $log->from = 'device';
        $log->save();
        return $log;
    }

    public static function logConversation(Device $device, string $from, string $type, string $xml, array $detail = []) {
        $enabled = boolval(Setting::getValue('conversation_log'));

        if($enabled && $device->debug === true) {
            $log = new static();
            $log->device_id = $device->id;
            $log->full_xml = $xml;
            $log->code = $type;
            $log->message = $type;
            $log->detail = $detail;
            $log->type = $type;
            $log->from = $from;
            $log->save();
        }
    }

    public function scopeFault(Builder $query): Builder {
        return $query->where('type', 'fault');
    }

    public function scopeType(Builder $query, string $type): Builder {
        return $query->where('type', $type);
    }

    public function scopeLast24Hours(Builder $query): Builder {
      return $query->where('created_at', '>=', now()->subDay());
    }

    public function scopeCreatedAfter(Builder $query, $date): Builder {
        return $query->where('created_at', '>=', $date);
    }

    public function prunable()
    {
        return static::where('created_at', '<=', now()->subDay());
    }
}
