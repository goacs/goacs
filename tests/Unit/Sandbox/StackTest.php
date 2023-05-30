<?php
declare(strict_types=1);

namespace Sandbox;

use App\ACS\Logic\Script\Stack;
use App\ACS\Types;
use Tests\TestCase;

class StackTest extends TestCase
{
    public function test_queue_tasks() {
        $stack = new Stack();

        $stack->add(Types::SetParameterValues, [
            'path' => 'A.B.C.D',
            'value' => 'val1',
            'flags' => 'RW',
            'type' => 'xsd:string',
        ]);
        $stack->add(Types::SetParameterValues, [
            'path' => 'A.B.C.E',
            'value' => 'val2',
            'flags' => 'RW',
            'type' => 'xsd:string',
        ]);
        $stack->add(Types::SetParameterValues, [
            'path' => 'A.B.C.F',
            'value' => 'val3',
            'flags' => 'RW',
            'type' => 'xsd:string',
        ]);
        $stack->add(Types::Commit, []);
        $stack->add(Types::SetParameterValues, [
            'path' => 'A.B.C.G',
            'value' => 'val4',
            'flags' => 'RW',
            'type' => 'xsd:string',
        ]);
        $stack->add(Types::SetParameterValues, [
            'path' => 'A.B.C.H',
            'value' => 'val5',
            'flags' => 'RW',
            'type' => 'xsd:string',
        ]);
        $stack->add(Types::Commit, []);
        $stack->add(Types::GetParameterValues, [
            'path' => 'A.B.C.G',
        ]);        $stack->add(Types::GetParameterValues, [
            'path' => 'A.B.C.F',
        ]);
        $stack->add(Types::Commit, []);


        dump($stack->groupByTaskType());
    }
}
