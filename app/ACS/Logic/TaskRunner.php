<?php

declare(strict_types=1);


namespace App\ACS\Logic;


use App\ACS\Context;
use App\ACS\Logic\Script\Sandbox;
use App\ACS\Logic\Script\SandboxException;
use App\Models\Log;

class TaskRunner
{
    private Context $context;
    private ?\App\ACS\Entities\Task $currentTask;

    public function __construct(Context $context) {
        $this->context = $context;
    }

    public function runNextTask() {
        $this->currentTask = $this->context->tasks->nextTask();
    }

    public function run() {
        if($this->currentTask === null) {
            Log::logConversation($this->context->deviceModel, 'acs', 'TaskRunner', 'No task to run');
            return;
        }


    }

    protected function runScriptTask() {
        $sandbox = new Sandbox($this->context, $this->currentTask->payload['script']);
        try {
            $sandbox->run();
            Log::logConversation($this->context->deviceModel,
                'acs',
                'Run Script',
                (string) $this->currentTask->payload['script'],
            );
        } catch (SandboxException $exception) {
            $fault = $this->context->deviceModel->faults()->make();
            $fault->full_xml = $exception->getTraceAsString();
            $fault->message = $exception->getMessage();
            $fault->code = '100020';
            $fault->save();
        }
        // Run other tasks
        $this->run();
    }
}
