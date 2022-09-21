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
use App\ACS\Request\InformRequest;
use App\ACS\Response\ErrorResponse;
use App\ACS\Response\TransferCompleteResponse;
use App\ACS\Types;
use App\ACS\XML\XMLParser;
use App\Models\Log;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Response;

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

        if($this->context->bodyType !== Types::INFORM && $this->context->newSession) {
            $this->context->response->setContent(
                (new ErrorResponse($this->context, 'invalid session'))->getBody()
            );
            return $this->context->response;
        }

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


        Log::logConversation($this->context,
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

            Log::logConversation($this->context,
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

            Log::logConversation($this->context,
                'acs',
                $this->context->acsRequest->getBaseName(),
                (string) $this->context->acsRequest->getBody(),
            );
        }

        $this->context->storeToSession();

        if( $this->context->response->getContent() === ""
            &&! $this->context->cpeRequest instanceof InformRequest
            && ! $this->context->acsResponse instanceof TransferCompleteResponse
            && ! $this->context->acsResponse instanceof ErrorResponse
            && $this->context->tasks->hasTasksToRun() === false) {
            $this->context->endSession();
        }

        return $this->context->response;
    }

    private function runTasks()
    {
        $taskRunner = new TaskRunner($this->context);
        $taskRunner->run();
    }

}
