<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'goacs:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install base system';

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
        $this->call('migrate:install');
        $this->call('migrate');
        $this->call('db:seed', ['--force']);
        return 0;
    }
}
