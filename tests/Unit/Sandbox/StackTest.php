<?php
declare(strict_types=1);

namespace Sandbox;

use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Logic\Script\Stack;
use App\ACS\Types;
use Tests\TestCase;

class StackTest extends TestCase
{
    public function test_queue_tasks()
    {
        $stack = new Stack();

        $stack->add(Types::SetParameterValues, [
            'parameters' => ParameterValuesCollection::fromArray([[
                'name' => 'A.B.C.D',
                'value' => 'val1',
                'flag' => 'RW',
                'type' => 'xsd:string',
            ]])
        ]);
        $stack->add(Types::SetParameterValues, [
            'parameters' => ParameterValuesCollection::fromArray([[
                'name' => 'A.B.C.E',
                'value' => 'val2',
                'flag' => 'RW',
                'type' => 'xsd:string',
            ]])
        ]);
        $stack->add(Types::SetParameterValues, [
            'parameters' => ParameterValuesCollection::fromArray([[
                'name' => 'A.B.C.F',
                'value' => 'val3',
                'flag' => 'RW',
                'type' => 'xsd:string',
            ]])
        ]);
        $stack->add(Types::Commit, []);
        $stack->add(Types::SetParameterValues, [
            'parameters' => ParameterValuesCollection::fromArray([[
                'name' => 'A.B.C.G',
                'value' => 'val4',
                'flag' => 'RW',
                'type' => 'xsd:string',
            ]])
        ]);
        $stack->add(Types::SetParameterValues, [
            'parameters' => ParameterValuesCollection::fromArray([[
                'name' => 'A.B.C.H',
                'value' => 'val5',
                'flag' => 'RW',
                'type' => 'xsd:string',
            ]])
        ]);
//        $stack->add(Types::Commit, []);
        $stack->add(Types::GetParameterValues, [
            'parameters' => ParameterValuesCollection::fromArray([[
                'name' => 'A.B.C.G',
            ]])
        ]);
        $stack->add(Types::GetParameterValues, [
            'parameters' => ParameterValuesCollection::fromArray([[
                'name' => 'A.B.C.F',
            ]])
        ]);
        $stack->add(Types::Commit, []);

        $grouped = $stack->groupByTaskType();

        dump($grouped);
        $this->assertCount(5, $grouped);
        $this->assertCount(3, $grouped[0]->payload['parameters']);
    }
}
