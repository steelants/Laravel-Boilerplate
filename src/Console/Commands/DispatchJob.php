<?php

namespace SteelAnts\LaravelBoilerplate\Console\Commands;

use Illuminate\Console\Command;

class DispatchJob extends Command
{
       /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:dispatch {job}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch job';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$job = $this->argument('job');
		$class = '\\App\\Jobs\\' . $job;
        if (!class_exists($class)){
            $class = '\\SteelAnts\\LaravelBoilerplate\\Jobs\\' . $job;
        }

        dispatch(new $class());
    }
}
