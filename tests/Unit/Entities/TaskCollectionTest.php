<?php


namespace Entities;


use App\ACS\Entities\Task;
use App\ACS\Entities\TaskCollection;
use App\ACS\Types;
use Tests\TestCase;

class TaskCollectionTest extends TestCase
{
    public function test_get_next_task() {
        $tc = $this->prepareTaskCollection();
        $nextTask = $tc->nextTask();
        $this->assertEquals('A.B.C.D.4', $nextTask->payload['parameter']);
        $nextTask->done();
        $nextTask = $tc->nextTask();
        $this->assertEquals('A.B.C.D.5', $nextTask->payload['parameter']);
    }

    public function test_get_prev_task() {
        $tc = $this->prepareTaskCollection();
        $prevTask = $tc->prevTask();
        $this->assertEquals('A.B.C.D.3', $prevTask->payload['parameter']);
        $tc->nextTask()->done();
        $prevTask = $tc->prevTask();
        $this->assertEquals('A.B.C.D.4', $prevTask->payload['parameter']);
    }

    private function prepareTaskCollection(): TaskCollection {
        $taskCollection = new TaskCollection();
        for($i = 0; $i < 10; $i++) {
            $task = new Task(Types::AddObject);
            $task->setPayload(['parameter' => 'A.B.C.D.'.$i]);

            if($i < 4) {
                $task->done();
            }
            $taskCollection->addTask($task);
        }

        return $taskCollection;
    }
}
