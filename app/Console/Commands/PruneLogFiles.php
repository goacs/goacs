<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Storage;

class PruneLogFiles extends Command
{
    const DIFF_TIME = 2 * 60 * 60; //2h in seconds;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:prune';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes old log files from store';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach(Storage::disk('logs')->listContents() as $file)
        {
            if($file['timestamp'] < now()->subSeconds(self::DIFF_TIME)->getTimestamp()) {
                Storage::disk('logs')->delete($file['path']);
            }
        }
        return 0;
    }
}
