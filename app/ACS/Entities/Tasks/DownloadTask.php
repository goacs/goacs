<?php
declare(strict_types=1);

namespace App\ACS\Entities\Tasks;

use App\ACS\ACSException;
use App\ACS\Context;
use App\ACS\Request\ACSRequest;
use App\ACS\Request\DownloadRequest;
use App\ACS\Types;
use App\Models\File;
use App\Models\Log;

class DownloadTask extends Task implements WithRequest
{
    public function __construct()
    {
        parent::__construct(Types::Download);
    }
    public function toRequest(Context $context): ACSRequest
    {
        $fileData = $this->getFileData($context, $this->payload['filename']);
        return new DownloadRequest($context, $this->payload['filetype'], $fileData['url'], $fileData['size']);
    }

    private function getFileData(Context $context, string $filename): array {
        $file = File::whereName($filename)->first();
        if($file === null || \Storage::disk($file->disk)->exists($file->filepath) === false) {
//            Log::logError($context, "Cannot find file in store: ".$filename);
            throw new ACSException("Cannot find file in store: ".$filename);
        }
        return [
            'url' => \Storage::disk($file->disk)->url($file->filepath),
            'size' => $file->size,
        ];
    }
}
