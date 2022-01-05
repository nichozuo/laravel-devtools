<?php

namespace Nichozuo\LaravelDevtools\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class BackupMenu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     */
    public function handle()
    {
        Artisan::call('iseed permissions --force');
        Artisan::call('iseed role_has_permissions --force');
        Artisan::call('iseed model_has_roles --force');
        Artisan::call('iseed personal_access_tokens --force');
    }
}
