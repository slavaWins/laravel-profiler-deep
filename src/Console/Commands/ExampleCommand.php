<?php

namespace ProfilerDeep\Console\Commands;

use ProfilerDeep\Library\ProfilerDeepHelper;
use ProfilerDeep\Models\ProfilerDeep;
use ProfilerDeep\Models\ProfilerDeepSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-profiler-deep:example';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заготовка команды laravel-profiler-deep';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {

        $this->info("laravel-profiler-deep - Команда выполнена");
    }
}
