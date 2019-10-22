<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\LiveHostController;

class SynchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synch:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synch local database to live server';

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
     * @return mixed
     */
    public function handle()
    {
        $ctrl = new LiveHostController();
    }
}
