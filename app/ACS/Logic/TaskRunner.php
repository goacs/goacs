<?php

declare(strict_types=1);


namespace App\ACS\Logic;


use App\ACS\Context;
use App\ACS\Entities\Tasks\Task;
use App\ACS\Logic\Processors\SetParameterValuesRequestProcessor;
use App\ACS\Logic\Script\Sandbox;
use App\ACS\Logic\Script\SandboxException;
use App\ACS\Request\AddObjectRequest;
use App\ACS\Request\DeleteObjectRequest;
use App\ACS\Request\DownloadRequest;
use App\ACS\Request\FactoryResetRequest;
use App\ACS\Request\GetParameterNamesRequest;
use App\ACS\Request\GetParameterValuesRequest;
use App\ACS\Request\RebootRequest;
use App\ACS\Request\SetParameterValuesRequest;
use App\ACS\Response\InformResponse;
use App\ACS\Response\TransferCompleteResponse;
use App\ACS\Types;
use App\Models\File;
use App\Models\Log;

class TaskRunner
{
    private Context $context;
    private ?\App\ACS\Entities\Tasks\Task $currentTask;

    public function __construct(Context $context) {
        $this->context = $context;
        $this->loadDeviceTasks();
        $this->selectNextTask();
    }

    public function selectNextTask() {
        $this->currentTask = $this->context->tasks->nextTask();
    }

    public function run() {
        try {
            //$taskName = $this->currentTask?->name ?? 'No task to run';
            //Log::logConversation($this->context->deviceModel, 'acs', 'TaskRunner', $taskName);
            if ($this->currentTask === null) {
                return;
            }

            /** @var Task $task */

            if ($this->currentTask->isOnRequest($this->context->bodyType) === false) {
                $this->context->tasks->add(clone $this->currentTask);
                $this->currentTask->done();
                $this->selectNextTask();
                $this->run();
            }


            switch ($this->currentTask->name) {
                case Types::INFORMResponse:
                    $acsResponse = (new InformResponse($this->context));
                    $this->context->acsResponse = $acsResponse;
                    break;

                case Types::GetParameterNames:
                    $acsRequest = (new GetParameterNamesRequest($this->context, $this->currentTask->payload['parameter']));
                    $this->context->acsRequest = $acsRequest;
                    break;


                case Types::GetParameterValues:
                    $request = new GetParameterValuesRequest($this->context);
                    $request->setParameters($this->currentTask->payload['parameters']);
                    $this->context->acsRequest = $request;
                    break;

                case Types::SetParameterValues:
                    $request = new SetParameterValuesRequest($this->context, $this->currentTask->payload['parameters']);
                    $this->context->acsRequest = $request;
                    break;

                case Types::AddObject:
                    $request = new AddObjectRequest($this->context, $this->currentTask->payload['parameter']);
                    $this->context->acsRequest = $request;
                    break;

                case Types::DeleteObject:
                    $request = new DeleteObjectRequest($this->context, $this->currentTask->payload['parameter']);
                    $this->context->acsRequest = $request;
                    break;

                case Types::Download:
                    $fileData = $this->getFileData($this->currentTask->payload['filename']);
                    $request = new DownloadRequest($this->context, $this->currentTask->payload['filetype'], $fileData['url'], $fileData['size']);
                    $this->context->acsRequest = $request;
                    break;

                case Types::Reboot:
                    $request = new RebootRequest($this->context);
                    $this->context->acsRequest = $request;
                    break;

                case Types::FactoryReset:
                    $request = new FactoryResetRequest($this->context);
                    $this->context->acsRequest = $request;
                    break;

                case Types::SetParameterValuesProcessor:
                    (new SetParameterValuesRequestProcessor($this->context))();
                    $this->currentTask->done();
                    $this->selectNextTask();
                    $this->run();
                    break;

                case Types::RunScript:
                    $this->runScriptTask();
                    $this->loadDeviceTasks();
                    $this->selectNextTask();
                    $this->run();
                    break;
            }

            if ($this->currentTask !== null) {
                $this->currentTask->done();
            }
        } catch (\Throwable $throwable) {
            Log::logError($this->context, $throwable->getMessage());
        }
    }


    private function loadDeviceTasks()
    {
        $tasks = $this->context->deviceModel->tasks()->get();
        foreach ($tasks as $task) {
            $this->context->tasks->addTask($task->toACSTask());
            $task->delete();
        }
    }
    protected function runScriptTask() {
        $sandbox = new Sandbox($this->context, $this->currentTask->payload['script']);
        try {
            $sandbox->execute();
            Log::logConversation($this->context,
                'acs',
                'Run Script',
                (string) $this->currentTask->payload['script'],
            );

        } catch (SandboxException $exception) {
            $fault = $this->context->deviceModel->faults()->make();
            $fault->full_xml = $exception->getMessage()."\n\n".$exception->getTraceAsString();
            $fault->message = $exception->getMessage();
            $fault->code = '100020';
            $fault->session_id = $this->context->sessionId;
            $fault->save();
        }
        // Run other tasks
        $this->currentTask->done();

    }

}
