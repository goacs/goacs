<?php


namespace App\Models;


use App\ACS\Entities\TaskCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Task
 *
 * @property int $id
 * @property string $for_type
 * @property int $for_id
 * @property string $on_request
 * @property string $name
 * @property string $payload
 * @property int $infinite
 * @property string $not_before
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Model|\Eloquent $morph
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Query\Builder|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereForId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereForType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereInfinite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereNotBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereOnRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Task withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Task withoutTrashed()
 * @mixin \Eloquent
 */
class Task extends Model
{
    use SoftDeletes;

    const TYPE_GLOBAL = 'global';

    protected $table = 'tasks';

    protected $casts = [
        'infinite' => 'bool',
        'payload' => 'array',
        'not_before' => 'date',
    ];

    public static function booted()
    {
        static::creating(function(self $model) {
            $model->infinite = false;
            $model->not_before = now();
        });
    }

    public function morph(): MorphTo {
        return $this->morphTo('for');
    }

    public function toACSTask(): \App\ACS\Entities\Task {
        $task = new \App\ACS\Entities\Task($this->name);
        $task->onRequest = $this->on_request;
        $task->setPayload((array) $this->payload);
        return $task;
    }

    public static function fromACSTask(\App\ACS\Entities\Task $acsTask): static {
        $task = new static();
        $task->payload = $acsTask->payload;
        $task->name = $acsTask->name;
        $task->on_request = $acsTask->onRequest;
        return $task;
    }

    public static function loadGlobalTasks(?string $name = null): TaskCollection {
        $tasksCollection = new TaskCollection();

        static::when($name !== null, fn($query) => $query->where('name', $name))
            ->where('for_type', static::TYPE_GLOBAL)
            ->get()
            ->each(fn(Task $task) => $tasksCollection->addTask($task->toACSTask()));

        return $tasksCollection;
    }
}
