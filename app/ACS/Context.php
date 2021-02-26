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
use App\ACS\Response\FaultResponse;
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
    const LOOKUP_PARAMS_PREFIX = 'LOOKUP_';
    const LOOKUP_PARAMS_ENABLED_PREFIX = 'LOOKUP_ENABLED_';

    /**
     * @var Request
     */
    public Request $request;
    /**
     * @var Response
     */
    public Response $response;

    public ?CPERequest $cpeRequest = null;

    public ?CPEResponse $cpeResponse = null;

    public ?ACSRequest $acsRequest = null;

    public ?ACSResponse $acsResponse = null;

    public ?Device $device = null;

    public ?DeviceModel $deviceModel;

    public ParameterInfoCollection $parameterInfos;

    public ParameterValuesCollection $parameterValues;

    public TaskCollection $tasks;

    public string $bodyType;

    public string $cwmpVersion;

    public bool $provision = false;

    public bool $lookupParameters = false;

    public bool $new = false;

    public bool $newSession = true;

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
                $this->lookupParameters = \Cache::get(self::LOOKUP_PARAMS_ENABLED_PREFIX.$this->device->serialNumber, false);
                if($this->cpeRequest->hasEvent(0) || $this->cpeRequest->hasEvent(1) || $this->lookupParameters) {
                    $this->provision = true;
                }
                break;

            case Types::GetParameterNamesResponse:
                $this->cpeResponse = new GetParameterNamesResponse($parser->body);
                $this->parameterInfos = $this->parameterInfos->merge($this->cpeResponse->parameters);
                $this->parameterValues = $this->parameterValues->merge($this->parameterInfos->toParameterValuesCollecton());
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
                $this->cpeResponse = new FaultResponse($parser);
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

        foreach ($this->session()->get('this', []) as $prop => $value) {
            $this->{$prop} = $value;
        }
    }

    public function storeToSession() {
        \Cache::put("SESSID_".$this->device->serialNumber, $this->session()->getId(),(5*60));
        $this->session()->put('device', $this->device);
        $this->session()->put('parameterNames', $this->parameterInfos);
        $this->session()->put('parameterValues', $this->parameterValues);
        $this->session()->put('tasks', $this->tasks);
        $this->session()->put('this', [
            'new' => $this->new,
            'provision' => $this->provision,
            'newSession' => false,
            'lookupParameters' => $this->lookupParameters,
        ]);
    }

}
