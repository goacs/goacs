<?php

declare(strict_types=1);


namespace App\ACS;


use App\ACS\Entities\Device;
use App\ACS\Entities\ParameterValuesCollection;
use App\ACS\Entities\Task;
use App\ACS\Request\ACSRequest;
use App\ACS\Request\CPERequest;
use App\ACS\Request\InformRequest;
use App\ACS\Response\ACSResponse;
use App\ACS\Response\CPEresponse;
use App\ACS\Response\GetParameterNamesResponse;
use App\ACS\Response\GetParameterValuesResponse;
use App\ACS\XML\XMLParser;
use App\Models\Device as DeviceModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

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

    public CPEresponse $cpeResponse;

    public ACSRequest $acsRequest;

    public ACSResponse $acsResponse;

    public ?Device $device = null;

    public ?DeviceModel $deviceModel;

    public string $bodyType;

    public string $cwmpVersion;

    public Collection $parameterNames;

    public ParameterValuesCollection $parameterValues;

    public Collection $tasks;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $this->configureResponse($response);
        $this->tasks = new Collection();
        $this->loadFromSession();
        $this->processBody();
    }

    public function envelopeId(): string
    {
        return time() . mt_rand(100000, 999999);
    }

    public function session()
    {
        return $this->request->session();
    }

    private function processBody()
    {
        $parser = new XMLParser((string)$this->request->getContent());
        $this->bodyType = $parser->bodyType;
        $this->cwmpVersion = $parser->cwmpVersion;

        switch ($this->bodyType) {
            case Types::INFORM:
                $this->cpeRequest = new InformRequest($parser->body);
                $this->device = $this->cpeRequest->device;
                break;
            case Types::GetParameterNamesResponse:
                dump("CURRENT PNC: ".$this->parameterNames->count());
                dump("CURRENT PNV: ".$this->parameterValues->count());
                $this->cpeResponse = new GetParameterNamesResponse($parser->body);
                $this->parameterNames = $this->cpeResponse->parameters;
                break;
            case Types::GetParameterValuesResponse:
                $this->cpeResponse = new GetParameterValuesResponse($parser->body);
                break;
        }
    }

    private function configureResponse(Response $response)
    {
        return $response->header('Content-Type', 'text/xml')
            ->header('Connection', 'Keep-alive');
    }

    public function loadFromSession()
    {
        $this->device = $this->session()->get('device', new Device());
        $this->parameterNames = $this->session()->get('parameterNames', new Collection());
        $this->parameterValues = $this->session()->get('parameterValues', new ParameterValuesCollection());
        $this->tasks = $this->session()->get('tasks', new Collection());
    }

    public function storeToSession() {
        $this->session()->put('device', $this->device);
        $this->session()->put('parameterNames', $this->parameterNames);
        $this->session()->put('parameterValues', $this->parameterValues);
        $this->session()->put('tasks', $this->tasks);
    }

    public function hasTaskOfType(string $type): bool {
        return $this->tasks->filter(fn(Task $task) => $task->name === $type)->count() > 0;
    }

    public function addTask(Task $task) {
        $this->tasks->push($task);
    }
}
