<?php

declare(strict_types=1);


namespace App\ACS;


use App\ACS\Entities\Device;
use App\ACS\Entities\ParameterInfoCollection;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\Task;
use App\ACS\Entities\TaskCollection;
use App\ACS\Request\ACSRequest;
use App\ACS\Request\CPERequest;
use App\ACS\Request\InformRequest;
use App\ACS\Request\TransferCompleteRequest;
use App\ACS\Response\ACSResponse;
use App\ACS\Response\AddObjectResponse;
use App\ACS\Response\CPEResponse;
use App\ACS\Response\DeleteObjectResponse;
use App\ACS\Response\DownloadResponse;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Response\GetParameterValuesResponse;
use App\ACS\Response\SetParameterValuesResponse;
use App\ACS\XML\XMLParser;
use App\Models\Device as DeviceModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use function Symfony\Component\Translation\t;

class Context
{
    /**
     * @var Request
     */
    public Request $request;
    /**
     * @var Response
     */
    public Response $response;

    public CPERequest $cpeRequest;

    public CPEResponse $cpeResponse;

    public ACSRequest $acsRequest;

    public ACSResponse $acsResponse;

    public ?Device $device = null;

    public ?DeviceModel $deviceModel;

    public string $bodyType;

    public string $cwmpVersion;

    public ParameterInfoCollection $parameterInfos;

    public ParameterValuesCollection $parameterValues;

    public TaskCollection $tasks;

    public string $requestId = '';

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $this->configureResponse($response);
        $this->loadFromSession();
        $this->processBody();
    }

    public function envelopeId(): string
    {
        if($this->requestId !== '') {
            return $this->requestId;
        }
        return (string) (time() . mt_rand(100000, 999999));
    }

    public function session()
    {
        try {
            return $this->request->session();
        } catch (\RuntimeException $exception) {
            return \session();
        }
    }

    private function processBody()
    {
        $parser = new XMLParser((string)$this->request->getContent());
        $this->bodyType = $parser->bodyType;
        $this->cwmpVersion = $parser->cwmpVersion;
        $this->requestId = $parser->requestId;

        switch ($this->bodyType) {
            case Types::INFORM:
                $this->cpeRequest = new InformRequest($parser->body);
                $this->device = $this->cpeRequest->device;
                break;

            case Types::GetParameterNamesResponse:
                $this->cpeResponse = new GetParameterNamesResponse($parser->body);
                $this->parameterInfos = $this->parameterInfos->merge($this->cpeResponse->parameters);
                break;

            case Types::GetParameterValuesResponse:
                $this->cpeResponse = new GetParameterValuesResponse($parser->body);
                $this->parameterValues = $this->parameterValues->merge($this->cpeResponse->parameters);
                $this->parameterValues->assignDefaultFlags($this->parameterInfos);
                break;

            case Types::SetParameterValuesResponse:
                $this->cpeResponse = new SetParameterValuesResponse($parser->body);
                break;

            case Types::AddObjectResponse:
                $this->cpeResponse = new AddObjectResponse($parser->body);
                break;

            case Types::DeleteObjectResponse:
                $this->cpeResponse = new DeleteObjectResponse($parser->body);
                break;

            case Types::DownloadResponse:
                $this->cpeResponse = new DownloadResponse($parser->body);
                break;

            case Types::TransferComplete:
                $this->cpeRequest = new TransferCompleteRequest($parser->body);
                break;

            case Types::FaultResponse:

                break;

        }
    }

    private function configureResponse(Response $response)
    {
        return $response
            ->header('Content-Type', 'text/xml')
            //->header('Connection', 'Keep-alive')
            ;
    }

    public function loadFromSession()
    {
        $this->device = $this->session()->get('device', new Device());
        $this->parameterInfos = $this->session()->get('parameterNames', new ParameterInfoCollection());
        $this->parameterValues = $this->session()->get('parameterValues', new ParameterValuesCollection());
        $this->tasks = $this->session()->get('tasks', new TaskCollection());

        if($this->device->serialNumber !== "") {
            $this->deviceModel = DeviceModel::whereSerialNumber($this->device->serialNumber)->first();
        }
    }

    public function storeToSession() {
        $this->session()->put('device', $this->device);
        $this->session()->put('parameterNames', $this->parameterInfos);
        $this->session()->put('parameterValues', $this->parameterValues);
        $this->session()->put('tasks', $this->tasks);
    }

}
