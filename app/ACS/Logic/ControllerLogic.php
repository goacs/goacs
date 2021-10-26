<?php

declare(strict_types=1);


namespace App\ACS\Logic;


use App\ACS\Context;
use App\ACS\Entities\ParameterInfoStruct;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\ParameterValueStruct;
use App\ACS\Entities\Task;
use App\ACS\Events\ParameterLookupDone;
use App\ACS\Logic\Processors\AddObjectResponseProcessor;
use App\ACS\Logic\Processors\DeleteObjectResponseProcessor;
use App\ACS\Logic\Processors\DownloadResponseProcessor;
use App\ACS\Logic\Processors\EmptyResponseProcessor;
use App\ACS\Logic\Processors\FaultResponseProcessor;
use App\ACS\Logic\Processors\GetParameterNamesResponseProcessor;
use App\ACS\Logic\Processors\GetParameterValuesResponseProcessor;
use App\ACS\Logic\Processors\GetRPCMethodsRequestProcessor;
use App\ACS\Logic\Processors\InformRequestProcessor;
use App\ACS\Logic\Processors\SetParameterValuesResponseProcessor;
use App\ACS\Logic\Processors\TransferCompleteProcessor;
use App\ACS\Logic\Script\Sandbox;
use App\ACS\Logic\Script\SandboxException;
use App\ACS\Request\AddObjectRequest;
use App\ACS\Request\DeleteObjectRequest;
use App\ACS\Request\DownloadRequest;
use App\ACS\Request\FactoryResetRequest;
use App\ACS\Request\GetParameterNamesRequest;
use App\ACS\Request\GetParameterValuesRequest;
use App\ACS\Request\InformRequest;
use App\ACS\Request\RebootRequest;
use App\ACS\Request\SetParameterValuesRequest;
use App\ACS\Response\AddObjectResponse;
use App\ACS\Response\DownloadResponse;
use App\ACS\Response\FaultResponse;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Response\GetRPCMethodsACSResponse;
use App\ACS\Response\InformResponse;
use App\ACS\Response\TransferCompleteResponse;
use App\ACS\Types;
use App\ACS\XML\XMLParser;
use App\Models\Device;
use App\Models\DeviceParameter;
use App\Models\Log;
use App\Models\File;
use App\Models\Setting;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ControllerLogic
{


    /**
     * @var Context
     */
    private Context $context;

    /**
     * @var Dispatcher
     */
    private Dispatcher $dispatcher;


    public function __construct(Context $context, Dispatcher $dispatcher) {
        $this->context = $context;
        $this->dispatcher = $dispatcher;
    }

    public function process(): Response {

        switch ($this->context->bodyType) {
            case Types::INFORM:
                (new InformRequestProcessor($this->context))();
                break;

            case Types::GetRPCMethodsRequest:
                (new GetRPCMethodsRequestProcessor($this->context))();
                break;

            case Types::EMPTY:
                (new EmptyResponseProcessor($this->context))();
                break;

            case Types::GetParameterNamesResponse:
                (new GetParameterNamesResponseProcessor($this->context))();
                break;

            case Types::GetParameterValuesResponse:
                (new GetParameterValuesResponseProcessor($this->context, $this->dispatcher))();
                break;

            case Types::SetParameterValuesResponse:
                (new SetParameterValuesResponseProcessor($this->context))();
                break;

            case Types::AddObjectResponse:
                (new AddObjectResponseProcessor($this->context))();
                break;

            case Types::DeleteObjectResponse:
                (new DeleteObjectResponseProcessor($this->context))();
                break;

            case Types::DownloadResponse:
                (new DownloadResponseProcessor($this->context))();
                break;

            case Types::TransferComplete:
                (new TransferCompleteProcessor($this->context))();
                break;

            case Types::FactoryResetResponse:
            case Types::RebootResponse:
                break;

            case Types::FaultResponse:
                (new FaultResponseProcessor($this->context))();
                break;

        }

        Log::logConversation($this->context->deviceModel,
            'device',
            $this->context->bodyType,
            (string) $this->context->request->getContent(),
        );

        if($this->context->provisioningCurrentState !== Context::PROVISIONING_STATE_ERROR) {
            $this->runTasks();
        }

        if($this->context->cpeRequest !== null) {
            if($this->context->acsResponse->type === 'text') {
                $body = $this->context->acsResponse->getBody();
            } else {
                $body = XMLParser::normalize($this->context->acsResponse->getBody());
            }

            $this->context
                ->response
                ->setContent($body)
            ;

            Log::logConversation($this->context->deviceModel,
                'acs',
                $this->context->acsResponse->getBaseName(),
                (string) $this->context->acsResponse->getBody(),
            );

        } else if($this->context->acsRequest !== null) {
            if($this->context->acsRequest->type === 'text') {
                $body = $this->context->acsRequest->getBody();
            } else {
                $body = XMLParser::normalize($this->context->acsRequest->getBody());
            }

            $this->context
                ->response
                ->setContent($body)
            ;

            Log::logConversation($this->context->deviceModel,
                'acs',
                $this->context->acsRequest->getBaseName(),
                (string) $this->context->acsRequest->getBody(),
            );
        }

        $this->context->storeToSession();
        return $this->context->response;
    }

    private function runTasks()
    {
        $this->loadDeviceTasks();
        /** @var Task $task */
        $task = $this->context->tasks->nextTask();
        if($task === null) {
            $this->endSession();
            return;
        }

        if($task->isOnRequest($this->context->bodyType) === false) {
            $this->context->tasks->add(clone $task);
            $task->done();
            $this->runTasks();
        }


        switch ($task->name) {
            case Types::INFORMResponse:
                $acsResponse = (new InformResponse($this->context));
                $this->context->acsResponse = $acsResponse;
                break;

            case Types::GetParameterNames:
                $acsRequest = (new GetParameterNamesRequest($this->context, $task->payload['parameter']));
                $this->context->acsRequest = $acsRequest;
                break;


            case Types::GetParameterValues:
                $request = new GetParameterValuesRequest($this->context);
                $request->setParameters($task->payload['parameters']);
                $this->context->acsRequest = $request;
                break;

            case Types::SetParameterValues:
                $request = new SetParameterValuesRequest($this->context, $task->payload['parameters']);
                $this->context->acsRequest = $request;
                break;

            case Types::AddObject:
                $request = new AddObjectRequest($this->context, $task->payload['parameter']);
                $this->context->acsRequest = $request;
                break;

            case Types::DeleteObject:
                $request = new DeleteObjectRequest($this->context, $task->payload['parameter']);
                $this->context->acsRequest = $request;
                break;

            case Types::Download:
                $fileData = $this->getFileData($task->payload['filename']);
                $request = new DownloadRequest($this->context, $task->payload['filetype'], $fileData['url'], $fileData['size']);
                $this->context->acsRequest = $request;
                break;

            case Types::TransferCompleteResponse:
                $response = new TransferCompleteResponse($this->context);
                $this->context->acsResponse = $response;
                break;

            case Types::Reboot:
                $request = new RebootRequest($this->context);
                $this->context->acsRequest = $request;
                break;

            case Types::FactoryReset:
                $request = new FactoryResetRequest($this->context);
                $this->context->acsRequest = $request;
                break;

            case Types::RunScript:
                $sandbox = new Sandbox($this->context, $task->payload['script']);
                try {
                    $sandbox->run();
                    Log::logConversation($this->context->deviceModel,
                        'acs',
                        'Run Script',
                        (string) $task->payload['script'],
                    );
                } catch (SandboxException $exception) {
                    $fault = $this->context->deviceModel->faults()->make();
                    $fault->full_xml = $exception->getTraceAsString();
                    $fault->message = $exception->getMessage();
                    $fault->code = '100020';
                    $fault->save();
                }
                $task->done();
                $this->runTasks();
                break;
        }

        $task->done();

    }

    private function endSession()
    {
        $root = $this->context->device->root;
        if($param = $this->context->parameterValues->get($root.'DeviceInfo.ProductClass')?->value) {
            $this->context->deviceModel->product_class = $param;
        }

        if($param = $this->context->parameterValues->get($root.'DeviceInfo.SoftwareVersion')?->value) {
            $this->context->deviceModel->software_version = $param;
        }

        if($param = $this->context->parameterValues->get($root.'DeviceInfo.HardwareVersion')?->value) {
            $this->context->deviceModel->hardware_version = $param;
        }

        $this->context->deviceModel->save();
        $this->context->flushSession();
    }


    private function loadDeviceTasks()
    {
        $tasks = $this->context->deviceModel->tasks()->get();
        foreach ($tasks as $task) {
            $this->context->tasks->addTask($task->toACSTask());
            $task->delete();
        }
    }

    private function getFileData(string $filename): array {
        $file = File::whereName($filename)->first();
        return [
            'url' => \Storage::disk($file->disk)->url($file->filepath),
            'size' => $file->size,
        ];
    }

}
