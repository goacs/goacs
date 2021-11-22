<?php

declare(strict_types=1);


namespace App\ACS\Logic\Processors;


use App\ACS\Entities\Task;
use App\ACS\Request\RebootRequest;
use App\ACS\Response\DownloadResponse;
use App\ACS\Types;

class DownloadResponseProcessor extends Processor
{

    public function __invoke()
    {
        /** @var DownloadResponse $response */
        $response = $this->context->cpeResponse;
        $this->context->tasks->flush();
        if($response->status === 1) {
            dump('Reboot needed to upgrade software');
            $this->context->tasks->addTask(new Task(Types::Reboot));
        }

    }
}
