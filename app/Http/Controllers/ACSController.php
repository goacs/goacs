<?php


namespace App\Http\Controllers;


use App\ACS\Context;
use App\ACS\Logic\ControllerLogic;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ACSController extends \Illuminate\Routing\Controller
{
    public function process(Request $request, Response $response, Dispatcher $dispatcher) {
        $context = new Context($request, $response);
        $logic = new ControllerLogic($context, $dispatcher);
        return $logic->process();
    }
}
